// Forum Mentions Alpine Component
export default function forumMentions({
    mentionables = [],
}) {
    return {
        mentionables,
        
        init() {
            console.log('Forum mentions Alpine component initialized');
            this.initializeMentions();
        },
        
        initializeMentions() {
            // Use the mentionables data passed to the component
            console.log('Initializing mentions with data:', this.mentionables);
            
            // Find RichEditor elements in the current Alpine component scope
            const richEditors = this.$el.querySelectorAll('.fi-fo-rich-editor, [data-field-wrapper-for*="content"]');
            console.log('Found rich editors:', richEditors.length);
            
            if (richEditors.length === 0) {
                // Fallback: look for any rich editor in the component
                const fallbackEditor = this.$el.querySelector('[x-data*="richEditorFormComponent"]');
                if (fallbackEditor) {
                    console.log('Found fallback rich editor');
                    this.setupFilamentMentions(fallbackEditor, this.mentionables);
                } else {
                    console.log('No rich editor found, trying direct setup');
                    this.setupFilamentMentions(this.$el, this.mentionables);
                }
                return;
            }
            
            richEditors.forEach((element) => {
                // Store mentionables on the element for later use
                element._mentionables = this.mentionables;
                
                // Find the Filament RichEditor Alpine component
                this.setupFilamentMentions(element, this.mentionables);
            });
        },
        
        setupFilamentMentions(element, mentionables) {
            // Find the Filament RichEditor container
            const richEditorContainer = element.closest('[x-data*="richEditorFormComponent"]');
            if (!richEditorContainer) {
                console.log('Could not find Filament RichEditor container, falling back to basic setup');
                this.observeForTipTapEditor(element);
                return;
            }
            
            console.log('Found Filament RichEditor container');
            
            // Wait for Alpine to initialize
            const checkAlpine = () => {
                if (richEditorContainer._x_dataStack && richEditorContainer._x_dataStack.length > 0) {
                    const alpineData = richEditorContainer._x_dataStack[0];
                    if (alpineData.$getEditor) {
                        console.log('Alpine RichEditor component found');
                        this.setupMentionsWithFilament(element, mentionables, alpineData);
                    } else {
                        setTimeout(checkAlpine, 100);
                    }
                } else {
                    setTimeout(checkAlpine, 100);
                }
            };
            
            checkAlpine();
        },
        
        setupMentionsWithFilament(element, mentionables, alpineData) {
            // Find the actual editor element (.ProseMirror)
            const editorElement = element.querySelector('.ProseMirror') || 
                                 element.closest('[x-data*="richEditorFormComponent"]').querySelector('.ProseMirror');
            
            if (!editorElement) {
                console.log('Could not find ProseMirror editor element');
                return;
            }
            
            console.log('Setting up mentions with Filament integration');
            
            // Store references for later use
            editorElement._mentionables = mentionables;
            editorElement._alpineData = alpineData;
            
            // Add input event listener
            editorElement.addEventListener('input', (e) => {
                this.handleMentionInputFilament(editorElement, mentionables, alpineData);
            });
            
            // Add keyup event listener for @ detection
            editorElement.addEventListener('keyup', (e) => {
                if (e.key === '@') {
                    console.log('@ typed in Filament editor');
                    this.handleMentionInputFilament(editorElement, mentionables, alpineData);
                }
            });
        },
        
        handleMentionInputFilament(editorElement, mentionables, alpineData) {
            const selection = window.getSelection();
            if (!selection.rangeCount) return;
            
            const range = selection.getRangeAt(0);
            if (!editorElement.contains(range.commonAncestorContainer)) return;
            
            // Get the text content before the cursor
            const textNode = range.startContainer;
            if (textNode.nodeType !== Node.TEXT_NODE) return;
            
            const textContent = textNode.textContent;
            const cursorPosition = range.startOffset;
            const textBeforeCursor = textContent.substring(0, cursorPosition);
            
            // Look for @ followed by text (mention pattern)
            const mentionMatch = textBeforeCursor.match(/@([a-zA-Z0-9_\s]*)$/);
            
            if (mentionMatch) {
                const query = mentionMatch[1].toLowerCase().trim();
                console.log('Mention query:', query);
                
                // Store the mention context for later insertion
                const atPosition = textBeforeCursor.lastIndexOf('@');
                editorElement._mentionContext = {
                    textNode: textNode,
                    atPosition: atPosition,
                    cursorPosition: cursorPosition,
                    query: query,
                    fullMatch: mentionMatch[0],
                    alpineData: alpineData
                };
                
                // Filter mentionables based on query
                const filteredMentionables = mentionables.filter(user => {
                    const userName = (user.name || '').toLowerCase();
                    return query === '' || userName.includes(query);
                });
                
                console.log('Filtered mentionables:', filteredMentionables);
                
                this.showMentionDropdown(editorElement, filteredMentionables, query);
            } else {
                // Clear stored context and hide dropdown if no mention pattern
                delete editorElement._mentionContext;
                this.hideMentionDropdown();
            }
        },
        
        observeForTipTapEditor(element) {
            // Fallback for non-Filament setup
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.type === 'childList') {
                        const editorElement = element.querySelector('.ProseMirror');
                        if (editorElement && !editorElement._mentionsInitialized) {
                            editorElement._mentionsInitialized = true;
                            console.log('TipTap editor found (fallback), mentionables available:', element._mentionables);
                            
                            editorElement.addEventListener('input', (e) => {
                                this.handleMentionInput(editorElement, element._mentionables || []);
                            });
                            
                            editorElement.addEventListener('keyup', (e) => {
                                if (e.key === '@') {
                                    console.log('@ typed, mentionables:', element._mentionables);
                                    this.handleMentionInput(editorElement, element._mentionables || []);
                                }
                            });
                            
                            observer.disconnect();
                        }
                    }
                });
            });
            
            observer.observe(element, {
                childList: true,
                subtree: true
            });
            
            // Also check immediately
            const editorElement = element.querySelector('.ProseMirror');
            if (editorElement && !editorElement._mentionsInitialized) {
                editorElement._mentionsInitialized = true;
                console.log('TipTap editor found immediately (fallback)');
                
                editorElement.addEventListener('input', (e) => {
                    this.handleMentionInput(editorElement, element._mentionables || []);
                });
                
                editorElement.addEventListener('keyup', (e) => {
                    if (e.key === '@') {
                        this.handleMentionInput(editorElement, element._mentionables || []);
                    }
                });
            }
        },
        
        handleMentionInput(editorElement, mentionables) {
            // Fallback mention handling for non-Filament setup
            const selection = window.getSelection();
            if (!selection.rangeCount) return;
            
            const range = selection.getRangeAt(0);
            if (!editorElement.contains(range.commonAncestorContainer)) return;
            
            const textNode = range.startContainer;
            if (textNode.nodeType !== Node.TEXT_NODE) return;
            
            const textContent = textNode.textContent;
            const cursorPosition = range.startOffset;
            const textBeforeCursor = textContent.substring(0, cursorPosition);
            
            const mentionMatch = textBeforeCursor.match(/@([a-zA-Z0-9_\s]*)$/);
            
            if (mentionMatch) {
                const query = mentionMatch[1].toLowerCase().trim();
                
                const atPosition = textBeforeCursor.lastIndexOf('@');
                editorElement._mentionContext = {
                    textNode: textNode,
                    atPosition: atPosition,
                    cursorPosition: cursorPosition,
                    query: query,
                    fullMatch: mentionMatch[0]
                };
                
                const filteredMentionables = mentionables.filter(user => {
                    const userName = (user.name || '').toLowerCase();
                    return query === '' || userName.includes(query);
                });
                
                this.showMentionDropdown(editorElement, filteredMentionables, query);
            } else {
                delete editorElement._mentionContext;
                this.hideMentionDropdown();
            }
        },
        
        hideMentionDropdown() {
            const existingDropdown = document.querySelector('.mention-dropdown');
            if (existingDropdown) {
                existingDropdown.remove();
            }
        },
        
        showMentionDropdown(editorElement, mentionables, query) {
            console.log('Showing mention dropdown with:', mentionables);
            
            // Remove any existing dropdown
            this.hideMentionDropdown();
            
            // Don't show dropdown if no matches
            if (!mentionables || mentionables.length === 0) {
                return;
            }
            
            // Create dropdown
            const dropdown = document.createElement('div');
            dropdown.className = 'mention-dropdown';
            
            dropdown.innerHTML = mentionables
                .slice(0, 5) // Limit to 5 users
                .map((user, index) => {
                    const userName = user.name || 'Unknown User';
                    const userId = user.id || 0;
                    
                    // Highlight matching text
                    const highlightedName = query ? 
                        userName.replace(new RegExp(`(${query})`, 'gi'), '<strong>$1</strong>') : 
                        userName;
                    
                    return `
                        <div class="mention-item" data-user-id="${userId}" data-user-name="${userName}">
                            <div class="avatar">
                                ${userName.charAt(0).toUpperCase()}
                            </div>
                            <span class="name">${highlightedName}</span>
                        </div>
                    `;
                })
                .join('');
            
            // Position dropdown near the cursor
            const rect = editorElement.getBoundingClientRect();
            dropdown.style.position = 'fixed';
            dropdown.style.top = (rect.bottom + 5) + 'px';
            dropdown.style.left = rect.left + 'px';
            
            // Add to document
            document.body.appendChild(dropdown);
            
            // Add click handlers
            dropdown.querySelectorAll('.mention-item').forEach((item) => {
                item.addEventListener('click', () => {
                    const userId = item.getAttribute('data-user-id');
                    const userName = item.getAttribute('data-user-name');
                    
                    console.log('User selected:', userId, userName);
                    
                    this.insertMentionFilament(editorElement, userId, userName);
                    this.hideMentionDropdown();
                });
            });
            
            // Close dropdown when clicking outside
            setTimeout(() => {
                document.addEventListener('click', function closeDropdown(e) {
                    if (!dropdown.contains(e.target)) {
                        this.hideMentionDropdown();
                        document.removeEventListener('click', closeDropdown);
                    }
                }.bind(this));
            }, 100);
        },
        
        insertMentionFilament(editorElement, userId, userName) {
            console.log('Attempting to insert mention with Filament:', userId, userName);
            
            const context = editorElement._mentionContext;
            if (!context) {
                console.error('No mention context available');
                return;
            }
            
            // Try using Filament's $getEditor() function first
            if (context.alpineData && context.alpineData.$getEditor) {
                const editor = context.alpineData.$getEditor();
                if (editor) {
                    console.log('Using Filament editor to insert mention');
                    
                    try {
                        // Delete the @query text first
                        editor.chain()
                            .focus()
                            .deleteRange({ 
                                from: editor.state.selection.from - context.fullMatch.length, 
                                to: editor.state.selection.from 
                            })
                            .run();
                        
                        // Insert the mention text as plain text (server will process it)
                        editor.chain()
                            .focus()
                            .insertContent(`@${userName} `)
                            .run();
                        
                        console.log('Mention inserted as plain text');
                        delete editorElement._mentionContext;
                        return;
                        
                    } catch (error) {
                        console.error('TipTap insertion failed:', error);
                    }
                }
            }
            
            // Fallback to DOM manipulation (this was working before)
            console.log('Falling back to DOM manipulation');
            this.insertMentionDOM(editorElement, userId, userName, context);
        },
        
        insertMentionDOM(editorElement, userId, userName, context) {
            try {
                const selection = window.getSelection();
                const range = selection.getRangeAt(0);
                
                // Find the text node containing the @ symbol
                let textNode = context.textNode;
                let startOffset = context.atPosition;
                let endOffset = context.cursorPosition;
                
                // Ensure we have a valid text node
                if (!textNode || textNode.nodeType !== Node.TEXT_NODE) {
                    console.log('Invalid text node, trying to find current text node');
                    textNode = range.startContainer;
                    if (textNode.nodeType !== Node.TEXT_NODE) {
                        textNode = textNode.childNodes[0];
                    }
                    
                    if (!textNode || textNode.nodeType !== Node.TEXT_NODE) {
                        console.error('Could not find valid text node');
                        return;
                    }
                    
                    // Recalculate positions
                    const textContent = textNode.textContent;
                    const atIndex = textContent.lastIndexOf('@');
                    if (atIndex === -1) {
                        console.error('Could not find @ symbol in text node');
                        return;
                    }
                    startOffset = atIndex;
                    endOffset = range.startOffset;
                }
                
                // Create a new range for replacement
                const replaceRange = document.createRange();
                replaceRange.setStart(textNode, startOffset);
                replaceRange.setEnd(textNode, endOffset);
                
                // Delete the @query text
                replaceRange.deleteContents();
                
                // Insert the plain text mention (server will process it)
                const mentionText = document.createTextNode(`@${userName} `);
                replaceRange.insertNode(mentionText);
                
                // Position cursor after the mention
                replaceRange.setStartAfter(mentionText);
                replaceRange.collapse(true);
                
                // Update selection
                selection.removeAllRanges();
                selection.addRange(replaceRange);
                
                console.log('Mention inserted via DOM manipulation');
                
                // Trigger input event to notify the editor
                setTimeout(() => {
                    const inputEvent = new Event('input', { bubbles: true });
                    editorElement.dispatchEvent(inputEvent);
                }, 10);
                
            } catch (error) {
                console.error('DOM manipulation insertion failed:', error);
                // If all else fails, just insert at current cursor position
                try {
                    document.execCommand('insertText', false, `@${userName} `);
                    console.log('Used execCommand fallback');
                } catch (execError) {
                    console.error('execCommand fallback also failed:', execError);
                }
            }
            
            // Clean up
            delete editorElement._mentionContext;
        },
    }
}
