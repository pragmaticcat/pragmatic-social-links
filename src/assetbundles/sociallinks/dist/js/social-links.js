(function() {
  if (window.PragmaticSocialLinksField) {
    return;
  }

  function parseJsonScript(root) {
    var node = root.querySelector('[data-network-map-json]');
    if (!node) {
      return [];
    }

    try {
      return JSON.parse(node.textContent || '[]');
    } catch (error) {
      console.error('Pragmatic Social Links: invalid network map JSON.', error);
      return [];
    }
  }

  function buildNetworkMap(items) {
    var map = {};
    for (var i = 0; i < items.length; i++) {
      map[items[i].handle] = items[i];
    }
    return map;
  }

  function buildIndexedName(name, index) {
    if (!name) {
      return name;
    }

    return name.replace(/\[(?:__INDEX__|\d+)\]\[(network|url)\]$/, '[' + index + '][$1]');
  }

  function SocialLinksField(root) {
    if (!root || root.dataset.pslReady === '1') {
      return;
    }

    this.root = root;
    this.root.dataset.pslReady = '1';
    this.tbody = root.querySelector('[data-rows]');
    this.template = root.querySelector('[data-row-template]');
    this.addButton = root.querySelector('[data-add-row]');
    this.networkByHandle = buildNetworkMap(parseJsonScript(root));
    this.dragRow = null;

    if (!this.tbody || !this.template || !this.addButton) {
      return;
    }

    this.bindExistingRows();
    this.updateEmptyState();
    this.reindexRows();

    this.addButton.addEventListener('click', this.addRow.bind(this));
  }

  SocialLinksField.prototype.bindExistingRows = function() {
    var rows = this.tbody.querySelectorAll('[data-row]');
    for (var i = 0; i < rows.length; i++) {
      this.attachRow(rows[i]);
    }
  };

  SocialLinksField.prototype.updateEmptyState = function() {
    var rows = this.tbody.querySelectorAll('[data-row]');
    var empty = this.tbody.querySelector('[data-empty-state]');

    if (rows.length > 0 && empty) {
      empty.remove();
      return;
    }

    if (rows.length === 0 && !empty) {
      var row = document.createElement('tr');
      row.dataset.emptyState = '1';
      row.innerHTML = '<td colspan="4" class="psl-empty">' + (this.root.dataset.emptyText || 'No social links yet.') + '</td>';
      this.tbody.appendChild(row);
    }
  };

  SocialLinksField.prototype.reindexRows = function() {
    var rows = this.tbody.querySelectorAll('[data-row]');

    for (var i = 0; i < rows.length; i++) {
      var inputs = rows[i].querySelectorAll('select,input');
      for (var j = 0; j < inputs.length; j++) {
        if (!inputs[j].name) {
          continue;
        }

        inputs[j].name = buildIndexedName(inputs[j].name, i);
      }
    }
  };

  SocialLinksField.prototype.renderPreview = function(row) {
    var select = row.querySelector('[data-network-select]');
    var preview = row.querySelector('[data-network-preview]');
    var item;

    if (!select || !preview) {
      return;
    }

    item = this.networkByHandle[select.value];
    if (!item) {
      preview.innerHTML = '';
      return;
    }

    preview.innerHTML = item.icon + '<span>' + item.label + '</span>';
  };

  SocialLinksField.prototype.attachRow = function(row) {
    var self = this;
    var removeButton = row.querySelector('[data-remove-row]');
    var select = row.querySelector('[data-network-select]');

    if (removeButton) {
      removeButton.addEventListener('click', function() {
        row.remove();
        self.updateEmptyState();
        self.reindexRows();
      });
    }

    if (select) {
      select.addEventListener('change', function() {
        self.renderPreview(row);
      });
    }

    row.addEventListener('dragstart', function() {
      self.dragRow = row;
      row.classList.add('psl-dragging');
    });

    row.addEventListener('dragend', function() {
      self.dragRow = null;
      row.classList.remove('psl-dragging');
      self.reindexRows();
    });

    row.addEventListener('dragover', function(event) {
      var rect;
      var before;

      event.preventDefault();
      if (!self.dragRow || self.dragRow === row) {
        return;
      }

      rect = row.getBoundingClientRect();
      before = event.clientY < rect.top + (rect.height / 2);
      self.tbody.insertBefore(self.dragRow, before ? row : row.nextSibling);
    });

    this.renderPreview(row);
  };

  SocialLinksField.prototype.addRow = function() {
    var fragment = this.template.content.cloneNode(true);
    var row = fragment.querySelector('[data-row]');

    if (!row) {
      return;
    }

    this.tbody.appendChild(fragment);
    this.attachRow(row);
    this.updateEmptyState();
    this.reindexRows();
  };

  function initAll(context) {
    var scope = context || document;
    var fields = scope.querySelectorAll('[data-social-links-field]');
    for (var i = 0; i < fields.length; i++) {
      new SocialLinksField(fields[i]);
    }
  }

  window.PragmaticSocialLinksField = {
    initAll: initAll,
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
      initAll(document);
    });
  } else {
    initAll(document);
  }
})();
