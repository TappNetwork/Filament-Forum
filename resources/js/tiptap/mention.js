// TipTap Mention Extension for Filament
// Waits for TipTap to be available and creates extension dynamically

// Wait for TipTap to be available in the global scope
function waitForTipTap() {
  return new Promise((resolve) => {
    function checkTipTap() {
      // Check multiple possible locations where TipTap might be available
      const tipTap = window.TipTap || window.tiptap;
      
      if (tipTap && tipTap.Node && tipTap.mergeAttributes) {
        resolve(tipTap);
      } else {
        // Also check if they're available as global functions
        const Node = window.Node;
        const mergeAttributes = window.mergeAttributes;
        
        if (Node && mergeAttributes && typeof Node.create === 'function') {
          resolve({ Node, mergeAttributes });
        } else {
          setTimeout(checkTipTap, 50);
        }
      }
    }
    checkTipTap();
  });
}

// Create the extension
const createMentionExtension = async () => {
  const { Node, mergeAttributes } = await waitForTipTap();
  
  return Node.create({
    name: 'mention',
    group: 'inline',
    inline: true,
    selectable: false,
    atom: true,

    addAttributes() {
      return {
        id: {
          default: null,
          parseHTML: element => element.getAttribute('data-id'),
          renderHTML: attributes => {
            if (!attributes.id) return {}
            return { 'data-id': attributes.id }
          },
        },
        label: {
          default: null,
          parseHTML: element => element.getAttribute('data-label'),
          renderHTML: attributes => {
            if (!attributes.label) return {}
            return { 'data-label': attributes.label }
          },
        },
        type: {
          default: 'mention',
          parseHTML: element => element.getAttribute('data-type'),
          renderHTML: attributes => {
            return { 'data-type': attributes.type || 'mention' }
          },
        },
      }
    },

    parseHTML() {
      return [{ tag: 'span[data-type="mention"]' }]
    },

    renderHTML({ HTMLAttributes }) {
      return [
        'span',
        mergeAttributes(
          { 'class': 'mention', 'data-type': 'mention' },
          HTMLAttributes
        ),
        `@${HTMLAttributes['data-label'] || ''}`,
      ]
    },

    renderText({ node }) {
      return `@${node.attrs.label}`
    },

    addCommands() {
      return {
        insertMention: (options) => ({ commands }) => {
          return commands.insertContent({
            type: this.name,
            attrs: options,
          })
        },
      }
    },
  });
};

// Export the promise that resolves to the extension
export default createMentionExtension()
