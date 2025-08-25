(() => {
  const API_BASE = '/api/v1';

  function buildHeaders() {
    return {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
    };
  }

  async function fetchJson(method, url, body) {
    try {
      const res = await fetch(API_BASE + url, {
        method,
        headers: buildHeaders(),
        body: body !== undefined ? JSON.stringify(body) : undefined,
      });

      if (res.status === 204) {
        return { ok: true, data: null };
      }

      let payload = null;
      try { payload = await res.json(); } catch (_) { payload = null; }

      if (res.ok) {
        // Backend already wraps as { success:true, data, pagination? }
        const data = payload && payload.data !== undefined ? payload.data : payload;
        const pagination = payload && payload.pagination ? payload.pagination : undefined;
        return { ok: true, data, pagination };
      }

      if (res.status === 422) {
        const err = payload && payload.error ? payload.error : {};
        return {
          ok: false,
          type: 'VALIDATION_ERROR',
          message: err.message || 'Dados inválidos',
          fields: err.fields || {},
        };
      }

      const err = payload && payload.error ? payload.error : {};
      return {
        ok: false,
        type: err.type || 'ERROR',
        message: err.message || 'Falha',
      };
    } catch (e) {
      return { ok: false, type: 'NETWORK_ERROR', message: 'Falha de rede' };
    }
  }

  async function listBooks(params = {}) {
    const qs = new URLSearchParams();
    Object.entries(params).forEach(([k, v]) => {
      if (v !== undefined && v !== null && String(v) !== '') qs.append(k, v);
    });
    return fetchJson('GET', '/books' + (qs.toString() ? `?${qs.toString()}` : ''));
  }

  async function getBook(id) {
    return fetchJson('GET', `/books/${id}`);
  }

  async function createBook(payload) {
    return fetchJson('POST', '/books', payload);
  }

  async function updateBook(id, payload) {
    return fetchJson('PUT', `/books/${id}`, payload);
  }

  async function deleteBook(id) {
    return fetchJson('DELETE', `/books/${id}`);
  }

  async function listAuthors() {
    return fetchJson('GET', '/authors-options');
  }

  // Authors CRUD
  async function listAuthorsCrud(params = {}) {
    const qs = new URLSearchParams();
    Object.entries(params).forEach(([k, v]) => { if (v !== undefined && v !== null && String(v) !== '') qs.append(k, v); });
    return fetchJson('GET', '/authors' + (qs.toString() ? `?${qs.toString()}` : ''));
  }
  async function getAuthor(id) { return fetchJson('GET', `/authors/${id}`); }
  async function createAuthor(payload) { return fetchJson('POST', '/authors', payload); }
  async function updateAuthor(id, payload) { return fetchJson('PUT', `/authors/${id}`, payload); }
  async function deleteAuthor(id) { return fetchJson('DELETE', `/authors/${id}`); }

  async function listSubjects() {
    return fetchJson('GET', '/subjects-options');
  }

  // Subjects CRUD
  async function listSubjectsCrud(params = {}) {
    const qs = new URLSearchParams();
    Object.entries(params).forEach(([k, v]) => { if (v !== undefined && v !== null && String(v) !== '') qs.append(k, v); });
    return fetchJson('GET', '/subjects' + (qs.toString() ? `?${qs.toString()}` : ''));
  }
  async function getSubject(id) { return fetchJson('GET', `/subjects/${id}`); }
  async function createSubject(payload) { return fetchJson('POST', '/subjects', payload); }
  async function updateSubject(id, payload) { return fetchJson('PUT', `/subjects/${id}`, payload); }
  async function deleteSubject(id) { return fetchJson('DELETE', `/subjects/${id}`); }

  async function getSummary() {
    return fetchJson('GET', '/reports/summary');
  }

  window.api = {
    fetchJson,
    listBooks,
    getBook,
    createBook,
    updateBook,
    deleteBook,
    listAuthors,
    listAuthorsCrud,
    getAuthor,
    createAuthor,
    updateAuthor,
    deleteAuthor,
    listSubjects,
    listSubjectsCrud,
    getSubject,
    createSubject,
    updateSubject,
    deleteSubject,
    getSummary,
  };
})();
