/**
 * SISTEMA DE CONTROL DE TAREAS ESCOLARES
 * 
 * HU02 - 12/05/2026: Tablero principal con creaciÃ³n de notas
 * HU03 - 13/05/2026: EdiciÃ³n y eliminaciÃ³n de notas
 * HU04 - 14/05/2026: Persistencia con localStorage
 * HU05 - 15/05/2026: Contenido enriquecido (negrita, cursiva, subrayado, listas, encabezados)
 * 
 * NOTA: localStorage es temporal hasta integrar backend en HU09
 */

document.addEventListener('DOMContentLoaded', () => {
    // ========== ESTADO DE LA APLICACIÃ“N ==========
    const state = {
        notes: [],
        currentNoteId: null,
        deleteNoteId: null
    };

    // ========== REFERENCIAS DEL DOM ==========
    const elements = {
        // Botones principales
        btnAddNote: document.getElementById('btnAddNote'),

        // Modal crear/editar
        modalOverlay: document.getElementById('modalOverlay'),
        modalTitle: document.getElementById('modalTitle'),
        btnCloseModal: document.getElementById('btnCloseModal'),
        btnCancelNote: document.getElementById('btnCancelNote'),
        btnSaveNote: document.getElementById('btnSaveNote'),
        btnDeleteNote: document.getElementById('btnDeleteNote'),

        // Modal confirmar eliminaciÃ³n
        confirmDeleteOverlay: document.getElementById('confirmDeleteOverlay'),
        btnCloseConfirm: document.getElementById('btnCloseConfirm'),
        btnCancelDelete: document.getElementById('btnCancelDelete'),
        btnConfirmDelete: document.getElementById('btnConfirmDelete'),

        // Formulario
        noteForm: document.getElementById('noteForm'),
        noteId: document.getElementById('noteId'),
        noteTitle: document.getElementById('noteTitle'),
        noteContent: document.getElementById('noteContent'),

        // Tablero
        notesGrid: document.getElementById('notesGrid'),
        emptyState: document.getElementById('emptyState'),

        // HU05: Barra de herramientas
        toolbarBtns: document.querySelectorAll('.toolbar-btn')
    };

    // ========== HU04: PERSISTENCIA CON LOCALSTORAGE ==========

    /**
     * Guarda las notas en localStorage
     * HU04 - 14/05/2026
     */
    function saveNotes() {
        try {
            localStorage.setItem('tareas_escolares_notas', JSON.stringify(state.notes));
        } catch (error) {
            console.error('âŒ Error al guardar en localStorage:', error);
        }
    }

    /**
     * Carga las notas desde localStorage
     * HU04 - 14/05/2026
     */
    /**
 * Carga las notas desde el servidor (MySQL)
 */
    function loadNotes() {
        fetch('backend/leer.php')
            .then(response => response.json())
            .then(data => {
                if (Array.isArray(data)) {
                    state.notes = data;
                    console.log(`ðŸ“¦ ${state.notes.length} notas cargadas desde MySQL`);
                    renderNotes();
                } else {
                    console.error('âŒ El servidor no devolviÃ³ un arreglo de notas:', data);
                }
            })
            .catch(error => {
                console.error('âŒ Error al cargar notas del servidor:', error);
            });
    }

    // ========== FUNCIONES PRINCIPALES ==========

    /**
     * Genera un ID Ãºnico
     * @returns {string}
     */
    function generateId() {
        return 'nota_' + Date.now().toString(36) + '_' + Math.random().toString(36).substr(2, 9);
    }

    /**
     * Abre el modal para crear nueva nota (HU02)
     */
    function openCreateModal() {
        state.currentNoteId = null;
        elements.noteId.value = '';
        elements.noteTitle.value = '';
        elements.noteContent.innerHTML = '';
        elements.modalTitle.textContent = 'Nueva Nota';
        elements.btnSaveNote.textContent = 'Guardar Nota';
        elements.btnDeleteNote.style.display = 'none';
        elements.modalOverlay.classList.add('active');
        elements.noteTitle.focus();
        resetToolbarState();
    }

    /**
     * Abre el modal para editar nota existente (HU03)
     * @param {string} noteId 
     */
    function openEditModal(noteId) {
        const note = state.notes.find(n => n.idnota === noteId);
        if (!note) return;

        state.currentNoteId = noteId;
        elements.noteId.value = note.idnota;
        elements.noteTitle.value = note.titulo;
        elements.noteContent.innerHTML = note.contenido;
        elements.modalTitle.textContent = 'Editar Nota';
        elements.btnSaveNote.textContent = 'Actualizar Nota';
        elements.btnDeleteNote.style.display = 'block';
        elements.modalOverlay.classList.add('active');
        elements.noteTitle.focus();
        resetToolbarState();
    }

    /**
     * Cierra el modal de crear/editar
     */
    function closeModal() {
        elements.modalOverlay.classList.remove('active');
        elements.noteForm.reset();
        elements.noteContent.innerHTML = '';
        state.currentNoteId = null;
        resetToolbarState();
    }

    /**
     * Crea una nueva nota (HU02)
     * @param {string} title 
     * @param {string} content - HTML enriquecido (HU05)
     */
    function createNote(title, content) {
        const note = {
            id: generateId(),
            title: title.trim(),
            content: content.trim(),
            createdAt: new Date().toISOString(),
            updatedAt: null
        };

        state.notes.unshift(note);
        saveNotes(); // HU04
        renderNotes();
    }

    /**
     * Actualiza una nota existente (HU03)
     * @param {string} id 
     * @param {string} title 
     * @param {string} content - HTML enriquecido (HU05)
     */
    function updateNote(id, title, content) {
        const index = state.notes.findIndex(n => n.id === id);
        if (index === -1) return;

        state.notes[index].title = title.trim();
        state.notes[index].content = content.trim();
        state.notes[index].updatedAt = new Date().toISOString();

        saveNotes(); // HU04
        renderNotes();
    }

    /**
     * Elimina una nota (HU03)
     * @param {string} id
    /**
     * Ejecuta la eliminaciÃ³n confirmada en el servidor (HU03)
     */
    function executeDelete() {
        if (!state.deleteNoteId) return;

        // Desactivamos el botÃ³n en el modal de confirmaciÃ³n mientras procesa
        elements.btnConfirmDelete.disabled = true;
        elements.btnConfirmDelete.textContent = 'Eliminando...';

        // ðŸš€ Enviamos el ID usando la variable exacta de tu base de datos: idnota
        fetch('backend/eliminar.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ idnota: state.deleteNoteId })
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    console.log('ðŸ—‘ï¸ Nota eliminada del servidor:', data.message);

                    // Borramos la nota de la interfaz de forma local
                    state.notes = state.notes.filter(n => n.idnota !== state.deleteNoteId);
                    state.deleteNoteId = null;

                    renderNotes();
                    closeModal();
                    elements.confirmDeleteOverlay.classList.remove('active');
                } else {
                    alert('âŒ Error al eliminar: ' + data.message);
                }
            })
            .catch(error => {
                console.error('âŒ Error en la peticiÃ³n Fetch:', error);
                alert('OcurriÃ³ un error al intentar comunicar con el servidor.');
            })
            .finally(() => {
                elements.btnConfirmDelete.disabled = false;
                elements.btnConfirmDelete.textContent = 'Eliminar';
            });
    }

    /**
     * Abre modal de confirmaciÃ³n para eliminar (HU03)
     * @param {string} noteId 
     */
    function confirmDelete(noteId) {
        state.deleteNoteId = noteId;
        elements.confirmDeleteOverlay.classList.add('active');
    }

    // ========== HU05: EDITOR ENRIQUECIDO ==========

    /**
     * Ejecuta un comando de formato en el editor
     * @param {string} command 
     * @param {string|null} value 
     */
    function execFormatCommand(command, value = null) {
        elements.noteContent.focus();
        document.execCommand(command, false, value);
        updateToolbarState();
    }

    /**
     * Actualiza el estado visual de los botones de la toolbar
     */
    function updateToolbarState() {
        elements.toolbarBtns.forEach(btn => {
            const command = btn.dataset.command;

            if (command === 'formatBlock') {
                // Para formatBlock, verificar el valor
                const currentFormat = document.queryCommandValue('formatBlock');
                btn.classList.toggle('active', currentFormat === btn.dataset.value);
            } else if (command === 'insertUnorderedList') {
                btn.classList.toggle('active', document.queryCommandState('insertUnorderedList'));
            } else if (command === 'insertOrderedList') {
                btn.classList.toggle('active', document.queryCommandState('insertOrderedList'));
            } else if (command !== 'removeFormat') {
                btn.classList.toggle('active', document.queryCommandState(command));
            }
        });
    }

    /**
     * Resetea el estado visual de la toolbar
     */
    function resetToolbarState() {
        elements.toolbarBtns.forEach(btn => {
            btn.classList.remove('active');
        });
    }

    /**
     * Obtiene texto plano del HTML para vista previa
     * @param {string} html 
     * @returns {string}
     */
    function getPlainTextPreview(html) {
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = html;

        // Reemplazar listas por texto con viÃ±etas
        tempDiv.querySelectorAll('li').forEach(li => {
            li.textContent = 'â€¢ ' + li.textContent;
        });

        // Reemplazar encabezados
        tempDiv.querySelectorAll('h3, h4').forEach(h => {
            h.textContent = h.textContent.toUpperCase() + ': ';
        });

        let text = tempDiv.textContent || tempDiv.innerText || '';
        text = text.replace(/\s+/g, ' ').trim();

        if (text.length > 120) {
            text = text.substring(0, 120) + '...';
        }

        return text;
    }

    /**
     * Formatea fecha para mostrar
     * @param {string} dateString 
     * @returns {string}
     */
    function formatDate(dateString) {
        const options = {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        };
        return new Date(dateString).toLocaleDateString('es-MX', options);
    }

    /**
     * Escapa HTML para prevenir XSS en tÃ­tulos
     * @param {string} text 
     * @returns {string}
     */
    function escapeHTML(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Renderiza todas las notas en el tablero
     */
    function renderNotes() {
        elements.notesGrid.innerHTML = '';

        if (state.notes.length === 0) {
            elements.emptyState.classList.remove('hidden');
        } else {
            elements.emptyState.classList.add('hidden');

            state.notes.forEach(note => {
                const card = createNoteCard(note);
                elements.notesGrid.appendChild(card);
            });
        }
    }

    /**
     * Crea elemento HTML de tarjeta de nota (HU02 + HU03 + HU05)
     * @param {Object} note 
     * @returns {HTMLElement}
     */
    function createNoteCard(note) {
        const card = document.createElement('div');
        card.className = 'note-card';
        card.dataset.noteId = note.idnota;

        // HU05: Vista previa con formato enriquecido
        let contentPreviewHTML = '';
        if (note.contenido) {
            // Crear vista previa limitada del HTML
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = note.contenido;

            // Limitar la cantidad de contenido visible
            const textLength = (tempDiv.textContent || '').length;
            if (textLength > 150) {
                contentPreviewHTML = getPlainTextPreview(note.contenido);
                // Usar texto plano para la vista previa larga
                contentPreviewHTML = `<p class="note-content-preview">${escapeHTML(contentPreviewHTML)}</p>`;
            } else {
                // Mostrar HTML enriquecido directamente
                contentPreviewHTML = `<div class="note-content-preview">${note.contenido}</div>`;
            }
        }

        // Cambia las lÃ­neas viejas de las fechas por esto:
        const createdDate = note.fecha ? formatDate(note.fecha) : 'Fecha desconocida';

        card.innerHTML = `
    <div class="note-card-actions">
        <button class="btn-card-action edit" data-action="edit" title="Editar nota">
            âœï¸
        </button>
        <button class="btn-card-action delete" data-action="delete" title="Eliminar nota">
            ðŸ—‘ï¸
        </button>
    </div>
    <h3 class="note-title">${escapeHTML(note.titulo)}</h3>
    ${contentPreviewHTML}
    <div class="note-meta">
        <span class="note-date">ðŸ• ${createdDate}</span>
    </div>
`;

        // Event listeners para los botones de la tarjeta (HU03)
        card.querySelector('[data-action="edit"]').addEventListener('click', (e) => {
            e.stopPropagation();
            openEditModal(note.idnota);
        });

        card.querySelector('[data-action="delete"]').addEventListener('click', (e) => {
            e.stopPropagation();
            confirmDelete(note.idnota);
        });

        // Click en la tarjeta abre ediciÃ³n
        card.addEventListener('click', () => {
            openEditModal(note.idnota);
        });

        return card;
    }

    /**
     * Maneja el envÃ­o del formulario (crear o actualizar en Base de Datos)
     * @param {Event} event 
     */
    function handleSubmit(event) {
        event.preventDefault();

        const title = elements.noteTitle.value;
        const content = elements.noteContent.innerHTML;

        if (!title.trim()) {
            alert('Por favor, escribe un tÃ­tulo para la nota.');
            elements.noteTitle.focus();
            return;
        }

        // Cambia la forma en que obtienes el idnota para asegurar que viaje al servidor:
        const datosNota = {
            idnota: elements.noteId.value || null, // <--- Lee directamente el input hidden del HTML
            titulo: title,
            contenido: content
        };

        elements.btnSaveNote.disabled = true;
        elements.btnSaveNote.textContent = 'Guardando...';

        fetch('backend/guardar.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(datosNota)
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    console.log('âœ… Nota guardada en el servidor:', data.message);

                    if (state.currentNoteId) {
                        // Modo EdiciÃ³n: Actualizamos la nota existente en la interfaz
                        const index = state.notes.findIndex(n => n.idnota === state.currentNoteId);
                        if (index !== -1) {
                            state.notes[index].titulo = title.trim();
                            state.notes[index].contenido = content.trim();
                        }
                    } else {
                        // Modo CreaciÃ³n: Recargamos las notas desde el servidor para traer el ID real generado por MySQL
                        // (Esto evita IDs temporales que rompen el editar/eliminar inmediatamente)
                        loadNotes();
                    }

                    renderNotes();
                    closeModal();
                } else {
                    alert('âŒ Error del servidor: ' + data.message);
                }
            })
            .catch(error => {
                console.error('âŒ Error en la peticiÃ³n Fetch:', error);
                alert('OcurriÃ³ un error al conectar con el servidor.');
            })
            .finally(() => {
                elements.btnSaveNote.disabled = false;
            });
    }

    // ========== EVENT LISTENERS ==========

    // Abrir modal crear (HU02)
    elements.btnAddNote.addEventListener('click', openCreateModal);

    // Cerrar modal crear/editar
    elements.btnCloseModal.addEventListener('click', closeModal);
    elements.btnCancelNote.addEventListener('click', closeModal);
    elements.modalOverlay.addEventListener('click', (e) => {
        if (e.target === elements.modalOverlay) closeModal();
    });

    // Cerrar modal confirmaciÃ³n
    elements.btnCloseConfirm.addEventListener('click', () => {
        elements.confirmDeleteOverlay.classList.remove('active');
        state.deleteNoteId = null;
    });
    elements.btnCancelDelete.addEventListener('click', () => {
        elements.confirmDeleteOverlay.classList.remove('active');
        state.deleteNoteId = null;
    });
    elements.confirmDeleteOverlay.addEventListener('click', (e) => {
        if (e.target === elements.confirmDeleteOverlay) {
            elements.confirmDeleteOverlay.classList.remove('active');
            state.deleteNoteId = null;
        }
    });

    // Confirmar eliminaciÃ³n (HU03)
    elements.btnConfirmDelete.addEventListener('click', executeDelete);

    // Enviar formulario
    elements.noteForm.addEventListener('submit', handleSubmit);

    // HU05: Event listeners de la toolbar
    elements.toolbarBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const command = btn.dataset.command;
            const value = btn.dataset.value || null;

            if (command === 'formatBlock' && value) {
                execFormatCommand(command, value);
            } else {
                execFormatCommand(command, value);
            }
        });
    });

    // HU05: Actualizar estado de toolbar al seleccionar texto o hacer clic en el editor
    elements.noteContent.addEventListener('keyup', updateToolbarState);
    elements.noteContent.addEventListener('mouseup', updateToolbarState);
    elements.noteContent.addEventListener('click', updateToolbarState);

    // HU05: Atajos de teclado para el editor
    elements.noteContent.addEventListener('keydown', (e) => {
        if (e.ctrlKey || e.metaKey) {
            switch (e.key.toLowerCase()) {
                case 'b':
                    e.preventDefault();
                    execFormatCommand('bold');
                    break;
                case 'i':
                    e.preventDefault();
                    execFormatCommand('italic');
                    break;
                case 'u':
                    e.preventDefault();
                    execFormatCommand('underline');
                    break;
            }
        }
    });

    // Cerrar modales con Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            if (elements.confirmDeleteOverlay.classList.contains('active')) {
                elements.confirmDeleteOverlay.classList.remove('active');
                state.deleteNoteId = null;
            } else if (elements.modalOverlay.classList.contains('active')) {
                closeModal();
            }
        }
    });

    // ========== INICIALIZACIÃ“N ==========
    loadNotes(); // HU04: Cargar notas al iniciar
    renderNotes();

    console.log('âœ… HU02 (12/05/2026): Tablero principal con creaciÃ³n de notas');
    console.log('âœ… HU03 (13/05/2026): EdiciÃ³n y eliminaciÃ³n de notas');
    console.log('âœ… HU04 (14/05/2026): Persistencia con localStorage');
    console.log('âœ… HU05 (15/05/2026): Editor enriquecido (negrita, cursiva, subrayado, listas, encabezados)');
    console.log(`ðŸ“ Total notas cargadas: ${state.notes.length}`);
});
