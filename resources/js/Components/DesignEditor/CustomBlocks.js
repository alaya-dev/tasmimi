/**
 * Custom blocks for GrapesJS Template Editor
 * Provides Arabic-friendly design elements for card templates
 */

export const customBlocks = {
    // Text Elements
    'arabic-text': {
        label: 'نص عربي',
        category: 'النصوص',
        content: {
            type: 'text',
            content: 'أدخل النص هنا',
            style: {
                'font-family': 'Cairo, Arial, sans-serif',
                'font-size': '16px',
                'color': '#333333',
                'padding': '10px',
                'direction': 'rtl',
                'text-align': 'right'
            },
            attributes: {
                'data-editable': 'true',
                'data-element-type': 'text',
                'data-field-name': 'نص'
            }
        }
    },

    'arabic-heading': {
        label: 'عنوان عربي',
        category: 'النصوص',
        content: {
            tagName: 'h1',
            type: 'text',
            content: 'عنوان رئيسي',
            style: {
                'font-family': 'Cairo, Arial, sans-serif',
                'font-size': '32px',
                'font-weight': 'bold',
                'color': '#1e293b',
                'margin': '10px 0',
                'direction': 'rtl',
                'text-align': 'center'
            },
            attributes: {
                'data-editable': 'true',
                'data-element-type': 'heading',
                'data-field-name': 'عنوان'
            }
        }
    },

    'name-field': {
        label: 'حقل الاسم',
        category: 'الحقول',
        content: {
            type: 'text',
            content: 'الاسم: _______________',
            style: {
                'font-family': 'Cairo, Arial, sans-serif',
                'font-size': '18px',
                'color': '#374151',
                'padding': '8px',
                'direction': 'rtl',
                'text-align': 'right',
                'border-bottom': '2px dotted #d1d5db'
            },
            attributes: {
                'data-editable': 'true',
                'data-element-type': 'name-field',
                'data-field-name': 'الاسم'
            }
        }
    },

    'date-field': {
        label: 'حقل التاريخ',
        category: 'الحقول',
        content: {
            type: 'text',
            content: 'التاريخ: __ / __ / ____',
            style: {
                'font-family': 'Cairo, Arial, sans-serif',
                'font-size': '16px',
                'color': '#374151',
                'padding': '8px',
                'direction': 'rtl',
                'text-align': 'right'
            },
            attributes: {
                'data-editable': 'true',
                'data-element-type': 'date-field',
                'data-field-name': 'التاريخ'
            }
        }
    },

    'occasion-field': {
        label: 'حقل المناسبة',
        category: 'الحقول',
        content: {
            type: 'text',
            content: 'المناسبة: _______________',
            style: {
                'font-family': 'Cairo, Arial, sans-serif',
                'font-size': '18px',
                'color': '#374151',
                'padding': '8px',
                'direction': 'rtl',
                'text-align': 'right',
                'border-bottom': '2px dotted #d1d5db'
            },
            attributes: {
                'data-editable': 'true',
                'data-element-type': 'occasion-field',
                'data-field-name': 'المناسبة'
            }
        }
    },

    // Media Elements
    'logo-placeholder': {
        label: 'شعار/لوجو',
        category: 'الوسائط',
        content: {
            type: 'image',
            style: {
                'width': '100px',
                'height': '100px',
                'object-fit': 'contain',
                'border': '2px dashed #d1d5db',
                'border-radius': '8px'
            },
            attributes: {
                'data-element-type': 'logo',
                'data-field-name': 'الشعار',
                'src': 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgdmlld0JveD0iMCAwIDEwMCAxMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIxMDAiIGhlaWdodD0iMTAwIiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik0zNSA2NUw1MCA0NUw2NSA2NUgzNVoiIGZpbGw9IiM5Q0EzQUYiLz4KPHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTQgMTZMNC41ODYgMTUuNDE0QTIgMiAwIDAgMSA3LjQxNCAxNS40MTRMOCA2TTE2IDE2TDE1LjQxNCAxNS40MTRBMSA0IDAgMCAwIDEyLjU4NiAxNS40MTRMOCA2TTggNkg4LjAxTTYgMjBIMThBMiAyIDAgMCAwIDIwIDE4VjZBMiAyIDAgMCAwIDE4IDRINkEyIDIgMCAwIDAgNCA2VjE4QTIgMiAwIDAgMCA2IDIwWiIgc3Ryb2tlPSIjOUNBM0FGIiBzdHJva2Utd2lkdGg9IjIiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCIvPgo8L3N2Zz4K'
            }
        }
    },

    'decorative-image': {
        label: 'صورة زخرفية',
        category: 'الوسائط',
        content: {
            type: 'image',
            style: {
                'width': '150px',
                'height': '100px',
                'object-fit': 'cover',
                'border-radius': '8px'
            },
            attributes: {
                'data-element-type': 'decorative-image',
                'data-field-name': 'صورة زخرفية',
                'src': 'https://via.placeholder.com/150x100/e2e8f0/64748b?text=صورة'
            }
        }
    },

    // Interactive Elements
    'arabic-button': {
        label: 'زر عربي',
        category: 'التفاعل',
        content: {
            tagName: 'button',
            type: 'text',
            content: 'انقر هنا',
            style: {
                'background': 'linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%)',
                'color': 'white',
                'padding': '12px 24px',
                'border': 'none',
                'border-radius': '8px',
                'font-family': 'Cairo, Arial, sans-serif',
                'font-size': '16px',
                'font-weight': '600',
                'cursor': 'pointer',
                'direction': 'rtl',
                'box-shadow': '0 4px 6px rgba(59, 130, 246, 0.3)'
            },
            attributes: {
                'data-editable': 'true',
                'data-element-type': 'button',
                'data-field-name': 'زر'
            }
        }
    },

    // Shapes and Decorations
    'decorative-line': {
        label: 'خط زخرفي',
        category: 'الأشكال',
        content: {
            tagName: 'div',
            style: {
                'width': '200px',
                'height': '2px',
                'background': 'linear-gradient(90deg, transparent 0%, #d1d5db 50%, transparent 100%)',
                'margin': '20px auto'
            },
            attributes: {
                'data-element-type': 'decorative-line',
                'data-field-name': 'خط زخرفي'
            }
        }
    },

    'decorative-border': {
        label: 'إطار زخرفي',
        category: 'الأشكال',
        content: {
            tagName: 'div',
            content: '<div style="padding: 20px; text-align: center; color: #6b7280;">محتوى الإطار</div>',
            style: {
                'border': '3px solid #e5e7eb',
                'border-radius': '12px',
                'background': 'rgba(249, 250, 251, 0.5)',
                'margin': '10px',
                'min-height': '100px',
                'position': 'relative'
            },
            attributes: {
                'data-element-type': 'decorative-border',
                'data-field-name': 'إطار زخرفي'
            }
        }
    },

    // Islamic/Arabic Decorations
    'islamic-pattern': {
        label: 'زخرفة إسلامية',
        category: 'الزخارف',
        content: {
            tagName: 'div',
            style: {
                'width': '80px',
                'height': '80px',
                'background': `url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23d1d5db' fill-opacity='0.4'%3E%3Cpath d='M30 30c0-16.569 13.431-30 30-30v60c-16.569 0-30-13.431-30-30z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E")`,
                'background-size': 'contain',
                'background-repeat': 'no-repeat',
                'background-position': 'center',
                'margin': '10px auto'
            },
            attributes: {
                'data-element-type': 'islamic-pattern',
                'data-field-name': 'زخرفة إسلامية'
            }
        }
    },

    // Watermark
    'watermark': {
        label: 'ختم الحقوق',
        category: 'الحماية',
        content: {
            type: 'text',
            content: 'سامقة © جميع الحقوق محفوظة',
            style: {
                'position': 'absolute',
                'bottom': '10px',
                'right': '10px',
                'font-family': 'Cairo, Arial, sans-serif',
                'font-size': '10px',
                'color': 'rgba(107, 114, 128, 0.6)',
                'background': 'rgba(255, 255, 255, 0.8)',
                'padding': '4px 8px',
                'border-radius': '4px',
                'pointer-events': 'none',
                'user-select': 'none',
                'z-index': '1000'
            },
            attributes: {
                'data-element-type': 'watermark',
                'data-field-name': 'ختم الحقوق',
                'data-locked': 'true'
            }
        }
    }
}

// Block categories in Arabic
export const blockCategories = [
    { id: 'النصوص', label: 'النصوص', icon: 'fas fa-font' },
    { id: 'الحقول', label: 'الحقول', icon: 'fas fa-edit' },
    { id: 'الوسائط', label: 'الوسائط', icon: 'fas fa-image' },
    { id: 'التفاعل', label: 'التفاعل', icon: 'fas fa-mouse-pointer' },
    { id: 'الأشكال', label: 'الأشكال', icon: 'fas fa-shapes' },
    { id: 'الزخارف', label: 'الزخارف', icon: 'fas fa-star-and-crescent' },
    { id: 'الحماية', label: 'الحماية', icon: 'fas fa-shield-alt' }
]

// Helper function to register all blocks
export const registerCustomBlocks = (editor) => {
    const blockManager = editor.BlockManager

    // Clear existing blocks
    blockManager.getAll().reset()

    // Add custom blocks
    Object.entries(customBlocks).forEach(([id, block]) => {
        blockManager.add(id, block)
    })

    return blockManager
}
