document.addEventListener("DOMContentLoaded", function () {
  const quantityInputs = document.querySelectorAll("input[name='qty']");
  quantityInputs.forEach(function (input) {
    input.addEventListener("change", function () {
      if (Number(input.value) < 1) {
        input.value = 1;
      }
    });
  });

  // Live search suggestions for product search inputs.
  const searchInputs = document.querySelectorAll("input[name='q']");
  searchInputs.forEach(initSearchSuggestions);

  function initSearchSuggestions(input) {
    const form = input.closest('form');
    if (!form) return;

    // Make the parent form position: relative so suggestion list can be positioned.
    form.style.position = form.style.position || 'relative';

    const list = document.createElement('ul');
    list.className = 'search-suggestions';
    list.setAttribute('role', 'listbox');
    list.style.display = 'none';
    form.appendChild(list);

    let activeIndex = -1;
    let items = [];

    const fetchSuggestions = debounce(async (term) => {
      if (!term) {
        hideSuggestions();
        return;
      }

      try {
        const url = `/hamropasal/products/search_suggestions.php?term=${encodeURIComponent(term)}`;
        const res = await fetch(url, {cache: 'no-store'});
        if (!res.ok) throw new Error('Fetch failed');
        const data = await res.json();
        items = data;
        renderSuggestions(term);
      } catch (err) {
        hideSuggestions();
      }
    }, 200);

    input.addEventListener('input', (event) => {
      const term = event.target.value.trim();
      activeIndex = -1;
      fetchSuggestions(term);
    });

    input.addEventListener('keydown', (event) => {
      if (list.style.display !== 'block' || !items.length) return;

      if (event.key === 'ArrowDown') {
        event.preventDefault();
        activeIndex = (activeIndex + 1) % items.length;
        updateActiveItem();
      } else if (event.key === 'ArrowUp') {
        event.preventDefault();
        activeIndex = (activeIndex - 1 + items.length) % items.length;
        updateActiveItem();
      } else if (event.key === 'Enter') {
        if (activeIndex >= 0 && activeIndex < items.length) {
          event.preventDefault();
          selectSuggestion(items[activeIndex]);
        }
      } else if (event.key === 'Escape') {
        hideSuggestions();
      }
    });

    document.addEventListener('click', (event) => {
      if (!form.contains(event.target)) {
        hideSuggestions();
      }
    });

    function renderSuggestions(term) {
      list.innerHTML = '';
      if (!items.length) {
        hideSuggestions();
        return;
      }

      items.forEach((item, index) => {
        const li = document.createElement('li');
        li.className = 'search-suggestion-item';
        li.setAttribute('role', 'option');
        li.setAttribute('data-index', index);
        li.innerHTML = `<strong>${highlight(item.name, term)}</strong>` +
          `<span class="search-suggestion-meta">${escapeHtml(item.category)} • Rs ${item.price.toFixed(2)}</span>`;

        li.addEventListener('click', () => {
          selectSuggestion(item);
        });

        list.appendChild(li);
      });

      activeIndex = -1;
      list.style.display = 'block';
    }

    function updateActiveItem() {
      const nodes = list.querySelectorAll('.search-suggestion-item');
      nodes.forEach((node, idx) => {
        node.classList.toggle('active', idx === activeIndex);
      });
      const active = nodes[activeIndex];
      if (active) {
        active.scrollIntoView({ block: 'nearest' });
      }
    }

    function selectSuggestion(item) {
      // Populate the search input and submit the form.
      input.value = item.name;
      hideSuggestions();
      form.submit();
    }

    function hideSuggestions() {
      list.style.display = 'none';
      activeIndex = -1;
      items = [];
    }

    function highlight(text, term) {
      if (!term) return escapeHtml(text);
      const escaped = escapeRegExp(term);
      const regex = new RegExp(`(${escaped})`, 'gi');
      return escapeHtml(text).replace(regex, '<mark>$1</mark>');
    }

    function escapeHtml(str) {
      return str.replace(/[&<>"]+/g, (match) => {
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' };
        return map[match] || match;
      });
    }

    function escapeRegExp(str) {
      return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    function debounce(fn, wait) {
      let timeout;
      return (...args) => {
        clearTimeout(timeout);
        timeout = setTimeout(() => fn(...args), wait);
      };
    }
  }

  // Hamburger menu toggle
  const hamburger = document.querySelector('.hamburger');
  const mainNav = document.querySelector('.main-nav');
  if (hamburger && mainNav) {
    hamburger.addEventListener('click', () => {
      mainNav.classList.toggle('open');
    });
    
    // Close menu when clicking outside
    document.addEventListener('click', (e) => {
      if (!hamburger.contains(e.target) && !mainNav.contains(e.target)) {
        mainNav.classList.remove('open');
      }
    });
  }

});
