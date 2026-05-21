import re
import sys

file_path = r'c:\xampp\htdocs\C-yGiaPh-\fe\src\components\DoiTac\CayGiaPha\index.vue'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Replace TreeItem
tree_item_start = content.find('const TreeItem = defineComponent({')
tree_item_end = content.find('});', tree_item_start) + 3

new_tree_item = '''const TreeItem = defineComponent({
    name: 'TreeItem',
    props: ['member', 'listDoiTocHo', 'searchQuery'],
    emits: ['edit'],
    render() {
        const hasChildren = this.member.children && this.member.children.length > 0;

        if (this.member.isDummy) {
            const nodeGroup = h('div', { class: 'tree-node-group-modern' }, [
                h('div', { 
                    class: 'tree-dummy-node',
                    style: 'width: 3px; height: 100px; background-color: #ef4444; margin: 0 auto;'
                })
            ]);
            const children = hasChildren ? h('ul', 
                this.member.children.map(child => h(TreeItem, { 
                    member: child, 
                    listDoiTocHo: this.listDoiTocHo, 
                    searchQuery: this.searchQuery,
                    onEdit: (m) => this.$emit('edit', m) 
                }))
            ) : null;
            return h('li', [nodeGroup, children]);
        }

        let cardTypeClass = 'card-default';
        if (!this.member.cha_id && this.member.loai_quan_he !== 'Vợ/Chồng') {
            cardTypeClass = 'card-root';
        } else if (this.member.gioi_tinh === 'Nữ') {
            cardTypeClass = 'card-female';
        } else if (this.member.gioi_tinh === 'Nam') {
            cardTypeClass = 'card-male';
        }

        const renderCard = (m, typeClass, isSpouse = false) => {
            const isHl = this.searchQuery && m.ho_ten && m.ho_ten.toLowerCase().includes(this.searchQuery.toLowerCase());
            return h('div', { 
                class: ['tree-modern-card', typeClass, { 'highlighted': isHl, 'is-dead': m.trang_thai === 'Đã mất' }],
                onClick: (e) => { e.stopPropagation(); this.$emit('edit', m); }
            }, [
                h('div', { class: 'card-badge' }, `ID: ${m.id || ''} - Đời ${m.doi_thu}`),
                h('div', { class: 'card-content' }, [
                    h('div', { class: 'card-name' }, m.ho_ten),
                    m.ngay_sinh ? h('div', { class: 'card-year' }, new Date(m.ngay_sinh).getFullYear()) : null
                ]),
                !isSpouse ? h('div', { class: 'card-actions' }, [
                    h('button', { class: 'btn-action btn-add', onClick: (e) => { e.stopPropagation(); this.$emit('edit', m); } }, h('i', { class: 'bx bx-plus' })),
                    h('button', { class: 'btn-action btn-expand', onClick: (e) => { e.stopPropagation(); this.$emit('edit', m); } }, h('i', { class: 'bx bx-chevron-down' }))
                ]) : null,
                h('button', { class: 'card-options-btn', onClick: (e) => { e.stopPropagation(); this.$emit('edit', m); } }, h('i', { class: 'bx bx-dots-vertical-rounded' }))
            ]);
        };

        const nodeGroup = h('div', { class: 'tree-node-group-modern' }, [
            renderCard(this.member, cardTypeClass, false),
            this.member.spouses && this.member.spouses.length ? this.member.spouses.map(spouse => {
                let spouseType = spouse.gioi_tinh === 'Nam' ? 'card-male' : (spouse.gioi_tinh === 'Nữ' ? 'card-female' : 'card-default');
                return [
                    h('div', { class: 'spouse-connector' }, [
                        h('i', { class: 'bx bxs-heart heart-icon' })
                    ]),
                    renderCard(spouse, spouseType, true)
                ];
            }) : null
        ]);
        
        const children = hasChildren ? h('ul', 
            this.member.children.map(child => h(TreeItem, { 
                member: child, 
                listDoiTocHo: this.listDoiTocHo, 
                searchQuery: this.searchQuery,
                onEdit: (m) => this.$emit('edit', m) 
            }))
        ) : null;
        
        return h('li', [nodeGroup, children]);
    }
});'''

content = content[:tree_item_start] + new_tree_item + content[tree_item_end:]

# Replace CSS
css_start = content.find('/* ─── CONNECTOR LINES (Đường liên kết dòng tộc) ─── */')
css_end = content.find('/* ─── LUXURY LIGHT MODAL CONTAINER ─── */')

new_css = '''/* ─── MODERN CONNECTOR LINES ─── */
.tree ul { padding-top: 50px; display: flex !important; flex-direction: row !important; flex-wrap: nowrap !important; justify-content: center; padding-left: 0; margin-bottom: 0; }
.tree li { text-align: center; list-style-type: none; padding: 50px 14px 0 14px; position: relative; flex: 0 0 auto !important; }

.tree li::before, .tree li::after {
    content: ''; position: absolute; top: 0; right: 50%;
    border-top: 3px solid #ef4444; width: 50%; height: 50px;
}
.tree li::after { right: auto; left: 50%; border-left: 3px solid #ef4444; }
.tree li:only-child::after, .tree li:only-child::before { display: none; }
.tree li:only-child { padding-top: 0; }
.tree li:first-child::before, .tree li:last-child::after { border: 0 none; }
.tree li:last-child::before { border-right: 3px solid #ef4444; border-radius: 0 4px 0 0; }
.tree li:first-child::after { border-radius: 4px 0 0 0; }
.tree ul ul::before {
    content: ''; position: absolute; top: 0; left: 50%;
    border-left: 3px solid #ef4444; width: 0; height: 50px;
}

.tree-node-group-modern { display: inline-flex; align-items: center; justify-content: center; position: relative; z-index: 10; }

.spouse-connector {
    width: 40px; height: 3px; background: #ef4444; position: relative; display: flex; align-items: center; justify-content: center;
}
.heart-icon {
    color: #ec4899; font-size: 18px; position: absolute; background: #fafafa; padding: 2px;
}

/* ─── MODERN CARD DESIGN ─── */
.tree-modern-card {
    background: #ffffff;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 16px 20px;
    min-width: 220px;
    position: relative;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
    cursor: pointer;
    transition: all 0.2s ease;
    text-align: center;
}
.tree-modern-card:hover { transform: translateY(-3px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }

.card-root { border-color: #f472b6; background-color: #fdf2f8; }
.card-female { border-color: #f472b6; background-color: #fdf2f8; }
.card-male { border-color: #60a5fa; background-color: #eff6ff; }
.card-default { border-color: #9ca3af; background-color: #f3f4f6; }

.card-badge {
    position: absolute;
    top: -12px;
    left: 16px;
    background: #ffffff;
    border: 1px solid #e5e7eb;
    padding: 2px 10px;
    border-radius: 12px;
    font-size: 11px;
    color: #6b7280;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    white-space: nowrap;
}

.card-content { margin-top: 4px; }
.card-name { font-weight: 700; font-size: 16px; color: #111827; }
.card-year { font-size: 12px; color: #4b5563; margin-top: 4px; }

.card-actions {
    position: absolute;
    bottom: -14px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 4px;
}
.btn-action {
    width: 28px; height: 28px; border-radius: 50%; border: 2px solid #ffffff; display: flex; align-items: center; justify-content: center; color: white; cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.btn-add { background: #10b981; }
.btn-expand { background: #ec4899; }

.card-options-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 24px; height: 24px;
    background: #8b5cf6;
    border: none; border-radius: 6px;
    color: white; display: flex; align-items: center; justify-content: center;
    cursor: pointer; opacity: 0; transition: opacity 0.2s;
}
.tree-modern-card:hover .card-options-btn { opacity: 1; }

.tree-modern-card.highlighted { border-color: #d97706 !important; background: rgba(217, 119, 6, 0.04) !important; animation: light-pulse 2s infinite; }
@keyframes light-pulse {
    0% { box-shadow: 0 0 0 0 rgba(217, 119, 6, 0.2); }
    70% { box-shadow: 0 0 0 10px rgba(217, 119, 6, 0); }
    100% { box-shadow: 0 0 0 0 rgba(217, 119, 6, 0); }
}
.tree-modern-card.is-dead { filter: grayscale(0.6); opacity: 0.65; }

'''

content = content[:css_start] + new_css + content[css_end:]

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
print('Done replacing.')
