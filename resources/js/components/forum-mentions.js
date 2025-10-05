// Alpine.js component for mention dropdown UI
// Uses DOM manipulation to insert mentions, server processes them into proper HTML

export default function forumMentions({
    mentionables = [],
}) {
    return {
        mentionables: mentionables,
        showDropdown: false,
        dropdownPosition: { top: 0, left: 0 },
        filteredMentionables: [],
        selectedIndex: 0,
        currentQuery: '',

    init() {
        this.observeForTipTapEditor();
        
        // Add cleanup when Alpine component is destroyed
        this.$el._x_cleanups = this.$el._x_cleanups || [];
        this.$el._x_cleanups.push(() => {
            this.cleanup();
        });
    },
        
    cleanup() {
        // Clean up any mention contexts and event listeners
        const editors = this.$el.querySelectorAll('.tiptap.ProseMirror');
        editors.forEach(editor => {
            if (editor._mentionContext) {
                delete editor._mentionContext;
            }
        });
        this.hideDropdown();
    },

        observeForTipTapEditor() {
            // Wait for TipTap editor to be ready
            setTimeout(() => {
                this.findAndObserveTipTapEditors();
            }, 100);
        },

        findAndObserveTipTapEditors() {
            // Look for Filament RichEditor containers within our scope
            const richEditorContainers = this.$el.querySelectorAll('.fi-fo-rich-editor');
            
            richEditorContainers.forEach((container, index) => {
                // Look for the actual TipTap editor element
                const editorElement = container.querySelector('.tiptap.ProseMirror');
                
                if (editorElement) {
                    this.setupMentionHandling(editorElement, container);
                } else {
                    // Retry after a short delay
                    setTimeout(() => {
                        const retryEditorElement = container.querySelector('.tiptap.ProseMirror');
                        if (retryEditorElement) {
                            this.setupMentionHandling(retryEditorElement, container);
                        }
                    }, 500);
                }
            });
        },

        setupMentionHandling(editorElement, container) {
            // Get the Alpine component that manages this editor
            const alpineComponent = container.closest('[x-data]');
            let alpineData = null;
            
            if (alpineComponent && alpineComponent._x_dataStack) {
                alpineData = alpineComponent._x_dataStack[0];
            }
            
            // Add event listeners
            editorElement.addEventListener('input', (event) => {
                this.handleMentionInput(event, editorElement, alpineData);
            });
            
            editorElement.addEventListener('keyup', (event) => {
                this.handleMentionInput(event, editorElement, alpineData);
            });
            
            editorElement.addEventListener('keydown', (event) => {
                if (this.showDropdown) {
                    this.handleDropdownKeydown(event, editorElement);
                }
            });
            
            // Handle clicks outside to close dropdown
            document.addEventListener('click', (event) => {
                if (this.showDropdown && !event.target.closest('.mention-dropdown') && !event.target.closest('.tiptap')) {
                    this.hideDropdown();
                }
            });
            
            // Prevent dropdown from closing when clicking inside it
            document.addEventListener('click', (event) => {
                if (event.target.closest('.mention-dropdown')) {
                    event.stopPropagation();
                }
            });
        },

        handleMentionInput(event, editorElement, alpineData) {
            const selection = window.getSelection();
            if (!selection.rangeCount) return;
            
            const range = selection.getRangeAt(0);
            const textNode = range.startContainer;
            
            if (textNode.nodeType !== Node.TEXT_NODE) return;
            
            const textContent = textNode.textContent;
            const cursorPosition = range.startOffset;
            
            // Look for @ symbol followed by characters
            const textBeforeCursor = textContent.substring(0, cursorPosition);
            const mentionMatch = textBeforeCursor.match(/@([a-zA-Z0-9_\s]*)$/);
            
            if (mentionMatch) {
                const query = mentionMatch[1];
                const fullMatch = mentionMatch[0];
                const atPosition = textBeforeCursor.lastIndexOf('@');
                
                // Store the current query
                this.currentQuery = query;
                
                // Store context for insertion
                editorElement._mentionContext = {
                    textNode,
                    atPosition,
                    cursorPosition,
                    fullMatch,
                    alpineData
                };
                
                this.filterMentionables(query);
                this.showMentionDropdown(editorElement, range);
            } else {
                this.hideDropdown();
                delete editorElement._mentionContext;
            }
        },

        filterMentionables(query) {
            if (!query) {
                this.filteredMentionables = this.mentionables.slice(0, 10);
            } else {
                this.filteredMentionables = this.mentionables.filter(user => 
                    user.name.toLowerCase().includes(query.toLowerCase())
                ).slice(0, 10);
            }

            this.selectedIndex = 0;
        },

        showMentionDropdown(editorElement, range) {
            // Get cursor position relative to the viewport
            const rect = range.getBoundingClientRect();
            
            // Check if this is an edit form by looking for specific container classes
            const isEditForm = editorElement.closest('[wire\\:submit="updateComment"]') !== null;
            
            if (isEditForm) {
                // For edit forms, use fixed positioning relative to viewport
                this.dropdownPosition = {
                    top: rect.bottom + 5,
                    left: rect.left
                };
            } else {
                // For main comment form, try to position relative to Alpine container
                const alpineContainer = editorElement.closest('[x-data*="forumMentions"]');
                
                if (alpineContainer) {
                    const containerRect = alpineContainer.getBoundingClientRect();
                    this.dropdownPosition = {
                        top: rect.bottom - containerRect.top + 5,
                        left: rect.left - containerRect.left
                    };
                } else {
                    // Fallback to fixed positioning
                    this.dropdownPosition = {
                        top: rect.bottom + 5,
                        left: rect.left
                    };
                }
            }
            
            this.showDropdown = true;
        },

        hideDropdown() {
            this.showDropdown = false;
            this.filteredMentionables = [];
            this.selectedIndex = 0;
            this.currentQuery = '';
            
            // Clean up mention context from editors in current scope
            const editorInScope = this.$el.querySelector('.tiptap.ProseMirror');
            if (editorInScope && editorInScope._mentionContext) {
                delete editorInScope._mentionContext;
            }
        },

        handleDropdownKeydown(event, editorElement) {
            switch (event.key) {
                case 'ArrowDown':
                    event.preventDefault();
                    this.selectedIndex = Math.min(this.selectedIndex + 1, this.filteredMentionables.length - 1);
                    break;
                case 'ArrowUp':
                    event.preventDefault();
                    this.selectedIndex = Math.max(this.selectedIndex - 1, 0);
                    break;
                case 'Enter':
                case 'Tab':
                    event.preventDefault();
                    if (this.filteredMentionables[this.selectedIndex]) {
                        this.insertMention(editorElement, this.filteredMentionables[this.selectedIndex].id, this.filteredMentionables[this.selectedIndex].name);
                    }
                    break;
                case 'Escape':
                    event.preventDefault();
                    this.hideDropdown();
                    break;
            }
        },

        clickMention(userId, userName, editorElement) {
            // Find the editor element if not provided
            if (!editorElement) {
                editorElement = this.$el.querySelector('.tiptap.ProseMirror');
            }
            
            if (editorElement) {
                this.insertMention(editorElement, userId, userName);
            } else {
                console.error('Could not find editor element for mention insertion');
            }
        },

        // Method called from the template
        selectMention(user) {
            // First try to find editor within this Alpine component scope
            const editorElement = this.$el.querySelector('.tiptap.ProseMirror');
            if (editorElement && editorElement._mentionContext) {
                console.log('Found editor with context in current scope');
                this.insertMention(editorElement, user.id, user.name);
                return;
            }
            
            // Find the editor that has mention context AND is within an Alpine scope that has showDropdown = true
            const allEditors = document.querySelectorAll('.tiptap.ProseMirror');
            
            // Prioritize editor within current Alpine scope even if it doesn't have context
            if (editorElement) {
                // Check if current Alpine scope has dropdown showing
                if (this.showDropdown) {
                    this.insertMentionFallback(user.name);
                    return;
                }
            }
            
            // Fallback: find any editor with context
            for (let editor of allEditors) {
                if (editor._mentionContext) {
                    this.insertMention(editor, user.id, user.name);
                    return;
                }
            }

            // Last resort: use current scope editor
            if (editorElement) {
                this.insertMentionFallback(user.name);
            } else {
                console.error('No editor found at all');
            }
        },

        insertMention(editorElement, userId, userName) {
            const context = editorElement._mentionContext;
            if (!context) {
                console.error('No mention context available');
                this.insertMentionFallback(userName);
                return;
            }
            
            try {
                // Focus the editor first
                editorElement.focus();
                
                // Use the stored context to replace text directly
                let textNode = context.textNode;
                let startOffset = context.atPosition;
                
                // Validate text node is still valid
                if (!textNode || !textNode.parentNode || textNode.nodeType !== Node.TEXT_NODE) {
                    console.log('Text node is no longer valid, using fallback');
                    this.insertMentionFallback(userName);
                    return;
                }
                
                // find the @ symbol and replace everything from @ to the end of the match
                const originalText = textNode.textContent;
                const beforeAtSymbol = originalText.substring(0, startOffset);
                const afterMatch = originalText.substring(startOffset + context.fullMatch.length);
                const newText = beforeAtSymbol + `@${userName} ` + afterMatch;
                
                // Update the text content
                textNode.textContent = newText;
                
                // Create a new selection at the end of the inserted mention
                const newCursorPosition = startOffset + `@${userName} `.length;
                
                // Create a new range and selection
                const selection = window.getSelection();
                selection.removeAllRanges();
                
                try {
                    const range = document.createRange();
                    range.setStart(textNode, newCursorPosition);
                    range.setEnd(textNode, newCursorPosition);
                    selection.addRange(range);
                } catch (rangeError) {
                    console.log('Range creation failed, but text was inserted:', rangeError.message);
                }
                
                // Trigger input event
                setTimeout(() => {
                    const inputEvent = new Event('input', { bubbles: true });
                    editorElement.dispatchEvent(inputEvent);
                }, 10);
                
            } catch (error) {
                console.error('Mention insertion failed:', error);
                this.insertMentionFallback(userName);
            }
            
            // Clean up
            delete editorElement._mentionContext;
            this.hideDropdown();
        },
        
        insertMentionFallback(userName) {
            try {
                // Try to find the current selection and replace @query with @username
                const selection = window.getSelection();
                if (selection.rangeCount > 0) {
                    const range = selection.getRangeAt(0);
                    const textNode = range.startContainer;
                    
                    if (textNode.nodeType === Node.TEXT_NODE) {
                        const textContent = textNode.textContent;
                        const cursorPosition = range.startOffset;
                        
                        // Look for @ symbol in the entire text content (not just before cursor)
                        const atIndex = textContent.indexOf('@');
                        
                        if (atIndex !== -1) {
                            // Replace the @ and any text after it with @username
                            const beforeAt = textContent.substring(0, atIndex);
                            const newText = beforeAt + `@${userName} `;
                            
                            textNode.textContent = newText;
                            
                            // Position cursor after the mention
                            const newCursorPosition = atIndex + `@${userName} `.length;
                            const newRange = document.createRange();
                            newRange.setStart(textNode, newCursorPosition);
                            newRange.setEnd(textNode, newCursorPosition);
                            selection.removeAllRanges();
                            selection.addRange(newRange);

                            this.hideDropdown();
                            return;
                        } else {
                            console.log('No @ symbol found in text content');
                        }
                    }
                }

                // Last resort: just insert the username (this will add to current position)
                document.execCommand('insertText', false, `${userName} `);
                
            } catch (error) {
                console.error('All fallback methods failed:', error);
            }
            
            // Clean up and hide dropdown
            const editorElements = document.querySelectorAll('.tiptap.ProseMirror');
            editorElements.forEach(el => delete el._mentionContext);
            this.hideDropdown();
        },

        highlightQuery(name, query) {
            if (!query) return name;
            
            const regex = new RegExp(`(${query})`, 'gi');
            return name.replace(regex, '<mark>$1</mark>');
        }
    }
}