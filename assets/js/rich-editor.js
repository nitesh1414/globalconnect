/**
 * Global Connect - Standalone WYSIWYG Rich Text Editor
 */

class RichTextEditor {
  constructor(textareaId) {
    this.textarea = document.getElementById(textareaId);
    if (!this.textarea) return;

    this.container = document.createElement('div');
    this.container.className = 'rich-editor-container';

    this.createToolbar();
    this.createEditableArea();

    // Hide original textarea and insert container
    this.textarea.style.display = 'none';
    this.textarea.parentNode.insertBefore(this.container, this.textarea);

    this.isSourceView = false;
    this.bindEvents();
  }

  createToolbar() {
    this.toolbar = document.createElement('div');
    this.toolbar.className = 'rich-editor-toolbar';
    this.toolbar.innerHTML = `
      <select class="editor-select" data-command="formatBlock" title="Heading Level">
        <option value="p">Paragraph</option>
        <option value="h2">Heading 2</option>
        <option value="h3">Heading 3</option>
        <option value="h4">Heading 4</option>
        <option value="h5">Heading 5</option>
        <option value="blockquote">Quote</option>
      </select>
      
      <div class="editor-divider"></div>

      <button type="button" class="editor-btn" data-command="bold" title="Bold (Ctrl+B)"><b>B</b></button>
      <button type="button" class="editor-btn" data-command="italic" title="Italic (Ctrl+I)"><i>I</i></button>
      <button type="button" class="editor-btn" data-command="underline" title="Underline (Ctrl+U)"><u>U</u></button>
      <button type="button" class="editor-btn" data-command="strikeThrough" title="Strikethrough"><s>S</s></button>

      <div class="editor-divider"></div>

      <button type="button" class="editor-btn" data-command="justifyLeft" title="Align Left">⯇</button>
      <button type="button" class="editor-btn" data-command="justifyCenter" title="Align Center">☰</button>
      <button type="button" class="editor-btn" data-command="justifyRight" title="Align Right">⯈</button>

      <div class="editor-divider"></div>

      <button type="button" class="editor-btn" data-command="insertUnorderedList" title="Bullet List">• List</button>
      <button type="button" class="editor-btn" data-command="insertOrderedList" title="Numbered List">1. List</button>

      <div class="editor-divider"></div>

      <button type="button" class="editor-btn" data-action="link" title="Insert Link">🔗 Link</button>
      <button type="button" class="editor-btn" data-action="image" title="Insert Image URL">🖼 Image</button>
      <button type="button" class="editor-btn" data-action="table" title="Insert Table">⊞ Table</button>

      <div class="editor-divider"></div>

      <button type="button" class="editor-btn" data-command="removeFormat" title="Clear Formatting">⌫ Clear</button>
      <button type="button" class="editor-btn" data-action="source" title="Toggle HTML Source Code">&lt;/&gt; Code</button>
    `;

    this.container.appendChild(this.toolbar);
  }

  createEditableArea() {
    this.editable = document.createElement('div');
    this.editable.className = 'rich-editor-content';
    this.editable.contentEditable = true;
    this.editable.innerHTML = this.textarea.value || '<p></p>';

    this.sourceView = document.createElement('textarea');
    this.sourceView.className = 'editor-html-source';

    this.container.appendChild(this.editable);
    this.container.appendChild(this.sourceView);
  }

  bindEvents() {
    // Toolbar command execution
    this.toolbar.addEventListener('click', (e) => {
      const btn = e.target.closest('.editor-btn');
      if (!btn) return;

      const cmd = btn.dataset.command;
      const action = btn.dataset.action;

      if (cmd) {
        document.execCommand(cmd, false, null);
        this.syncContent();
      } else if (action) {
        this.handleAction(action);
      }
    });

    // Format block select change
    const formatSelect = this.toolbar.querySelector('select[data-command="formatBlock"]');
    if (formatSelect) {
      formatSelect.addEventListener('change', (e) => {
        const val = e.target.value;
        document.execCommand('formatBlock', false, `<${val}>`);
        this.syncContent();
      });
    }

    // Sync on input
    this.editable.addEventListener('input', () => this.syncContent());
    this.editable.addEventListener('blur', () => this.syncContent());

    this.sourceView.addEventListener('input', () => {
      this.editable.innerHTML = this.sourceView.value;
      this.textarea.value = this.sourceView.value;
    });

    // Form submission sync
    const form = this.textarea.closest('form');
    if (form) {
      form.addEventListener('submit', () => this.syncContent());
    }
  }

  handleAction(action) {
    if (action === 'link') {
      const url = prompt('Enter URL (e.g. https://... or /page.php?slug=...):');
      if (url) {
        document.execCommand('createLink', false, url);
        this.syncContent();
      }
    } else if (action === 'image') {
      const url = prompt('Enter Image URL (e.g. /assets/images/... or https://...):');
      if (url) {
        document.execCommand('insertImage', false, url);
        this.syncContent();
      }
    } else if (action === 'table') {
      const tableHtml = `
        <table class="table table-bordered">
          <thead>
            <tr><th>Header 1</th><th>Header 2</th><th>Header 3</th></tr>
          </thead>
          <tbody>
            <tr><td>Cell 1</td><td>Cell 2</td><td>Cell 3</td></tr>
            <tr><td>Cell 4</td><td>Cell 5</td><td>Cell 6</td></tr>
          </tbody>
        </table><p></p>
      `;
      document.execCommand('insertHTML', false, tableHtml);
      this.syncContent();
    } else if (action === 'source') {
      this.toggleSource();
    }
  }

  toggleSource() {
    this.isSourceView = !this.isSourceView;
    if (this.isSourceView) {
      this.sourceView.value = this.editable.innerHTML;
      this.editable.style.display = 'none';
      this.sourceView.style.display = 'block';
    } else {
      this.editable.innerHTML = this.sourceView.value;
      this.sourceView.style.display = 'none';
      this.editable.style.display = 'block';
    }
    this.syncContent();
  }

  syncContent() {
    if (this.isSourceView) {
      this.textarea.value = this.sourceView.value;
    } else {
      this.textarea.value = this.editable.innerHTML;
    }
  }
}

// Auto-initialize editors with class .rich-editor or id editor
document.addEventListener('DOMContentLoaded', () => {
  const textareas = document.querySelectorAll('.rich-editor, #content');
  textareas.forEach(ta => new RichTextEditor(ta.id));
});
