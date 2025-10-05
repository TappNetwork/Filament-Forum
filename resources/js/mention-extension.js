// TipTap Mention Extension for Filament Forum
// This extension allows TipTap to recognize and render mention nodes

import { Node, mergeAttributes } from '@tiptap/core'

export const Mention = Node.create({
  name: 'mention',

  addOptions() {
    return {
      HTMLAttributes: {},
    }
  },

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
          if (!attributes.id) {
            return {}
          }
          return {
            'data-id': attributes.id,
          }
        },
      },
      label: {
        default: null,
        parseHTML: element => element.getAttribute('data-label'),
        renderHTML: attributes => {
          if (!attributes.label) {
            return {}
          }
          return {
            'data-label': attributes.label,
          }
        },
      },
      type: {
        default: 'mention',
        parseHTML: element => element.getAttribute('data-type'),
        renderHTML: attributes => {
          return {
            'data-type': attributes.type || 'mention',
          }
        },
      },
    }
  },

  parseHTML() {
    return [
      {
        tag: 'span[data-type="mention"]',
      },
    ]
  },

  renderHTML({ node, HTMLAttributes }) {
    return [
      'span',
      mergeAttributes(
        { 
          'class': 'mention',
          'data-type': 'mention' 
        },
        this.options.HTMLAttributes,
        HTMLAttributes
      ),
      `@${HTMLAttributes['data-label'] || ''}`,
    ]
  },

  renderText({ node }) {
    return `@${node.attrs.label || ''}`
  },

  addKeyboardShortcuts() {
    return {
      Backspace: () =>
        this.editor.commands.command(({ tr, state }) => {
          let isMention = false
          const { selection } = state
          const { empty, anchor } = selection

          if (!empty) {
            return false
          }

          state.doc.nodesBetween(anchor - 1, anchor, (node, pos) => {
            if (node.type.name === this.name) {
              isMention = true
              tr.insertText('', pos, pos + node.nodeSize)
              return false
            }
          })

          return isMention
        }),
    }
  },
})

export default Mention
