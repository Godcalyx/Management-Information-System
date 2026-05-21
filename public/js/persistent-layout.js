(function () {
  const SCRIPT_TYPES = new Set([
    '',
    'text/javascript',
    'application/javascript',
    'module',
  ]);

  let isNavigating = false;

  function shouldHandleLink(link) {
    if (!link || !link.href) {
      return false;
    }

    if (link.target && link.target !== '_self') {
      return false;
    }

    if (link.hasAttribute('download') || link.dataset.noPersistent === 'true') {
      return false;
    }

    if (link.closest('[data-bs-toggle]')) {
      return false;
    }

    const href = link.getAttribute('href');
    if (!href || href.startsWith('#') || href.startsWith('javascript:')) {
      return false;
    }

    const url = new URL(link.href, window.location.href);
    if (url.origin !== window.location.origin) {
      return false;
    }

    if (url.pathname === window.location.pathname && url.search === window.location.search) {
      return false;
    }

    return !/(\/export(?:\/|$)|\/download(?:\/|$)|\/preview(?:\/|$)|\.(?:pdf|csv|xlsx|xls|zip)$)/i.test(url.pathname);
  }

  function cloneAndExecuteScript(script) {
    const type = (script.getAttribute('type') || '').trim();
    if (!SCRIPT_TYPES.has(type)) {
      return;
    }

    const clone = document.createElement('script');
    for (const { name, value } of Array.from(script.attributes)) {
      clone.setAttribute(name, value);
    }
    clone.textContent = script.textContent;
    script.replaceWith(clone);
  }

  function executeScriptsWithin(container) {
    if (!container) {
      return;
    }

    container.querySelectorAll('script').forEach(cloneAndExecuteScript);
  }

  async function navigateTo(url, options = {}) {
    const { push = true } = options;
    const currentLayout = document.body.dataset.layoutShell;
    const currentContentScripts = document.querySelector('[data-page-scripts]');

    if (!currentLayout || isNavigating) {
      window.location.href = url;
      return;
    }

    isNavigating = true;
    document.body.dataset.navLoading = 'true';

    try {
      const response = await fetch(url, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'X-Persistent-Nav': 'true',
        },
      });

      const contentType = response.headers.get('content-type') || '';
      if (!response.ok || !contentType.includes('text/html')) {
        window.location.href = url;
        return;
      }

      const html = await response.text();
      const parser = new DOMParser();
      const incomingDocument = parser.parseFromString(html, 'text/html');

      if (incomingDocument.body?.dataset.layoutShell !== currentLayout) {
        window.location.href = url;
        return;
      }

      const replacedFragments = [];
      document.querySelectorAll('[data-shell-fragment]').forEach((currentFragment) => {
        const fragmentName = currentFragment.getAttribute('data-shell-fragment');
        const incomingFragment = incomingDocument.querySelector(`[data-shell-fragment="${fragmentName}"]`);

        if (!incomingFragment) {
          return;
        }

        const replacement = incomingFragment.cloneNode(true);
        currentFragment.replaceWith(replacement);
        replacedFragments.push(replacement);
      });

      if (currentContentScripts) {
        const incomingScripts = incomingDocument.querySelector('[data-page-scripts]');
        currentContentScripts.innerHTML = incomingScripts ? incomingScripts.innerHTML : '';
        executeScriptsWithin(currentContentScripts);
      }

      replacedFragments.forEach(executeScriptsWithin);

      document.title = incomingDocument.title || document.title;

      if (push) {
        window.history.pushState({ url }, '', url);
      }

      window.scrollTo({ top: 0, left: 0, behavior: 'auto' });
      window.dispatchEvent(new CustomEvent('persistent-nav:render', { detail: { url } }));
    } catch (error) {
      window.location.href = url;
    } finally {
      document.body.removeAttribute('data-nav-loading');
      isNavigating = false;
    }
  }

  document.addEventListener('click', (event) => {
    if (event.defaultPrevented || event.button !== 0) {
      return;
    }

    if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
      return;
    }

    const link = event.target.closest('a[href]');
    if (!shouldHandleLink(link)) {
      return;
    }

    event.preventDefault();
    navigateTo(link.href);
  });

  window.addEventListener('popstate', () => {
    navigateTo(window.location.href, { push: false });
  });
})();
