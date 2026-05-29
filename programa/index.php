<?php
require_once 'backend/conexion.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Control de Tareas Escolares</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <!-- ========== HEADER ========== -->
    <header class="header">
        <div class="header-left">
            <span class="logo-icon">Ã°Å¸â€œÅ¡</span>
            <h1 class="app-title">Tareas Escolares</h1>
        </div>
        <div class="header-right">
            <span class="subtitle">Organiza tus notas</span>
        </div>
    </header>

    <!-- ========== CONTENIDO PRINCIPAL ========== -->
    <main class="main-container">
        <!-- BotÃƒÂ³n crear nueva nota (HU02 - 12/05/2026) -->
        <div class="add-note-section">
            <button class="btn-add-note" id="btnAddNote">
                <span class="btn-icon-plus">+</span>
                Nueva Nota
            </button>
        </div>

        <!-- Tablero de notas -->
        <div class="board" id="notesBoard">
            <!-- Estado vacÃƒÂ­o -->
            <div class="empty-state" id="emptyState">
                <div class="empty-state-icon">Ã°Å¸â€œÂ</div>
                <h3>No hay notas todavÃƒÂ­a</h3>
                <p>Haz clic en "Nueva Nota" para crear tu primera nota</p>
            </div>

            <!-- Grid de tarjetas -->
            <div class="notes-grid" id="notesGrid">
                <!-- Las tarjetas se generan dinÃƒÂ¡micamente -->
            </div>
        </div>
    </main>

    <!-- ========== MODAL CREAR/EDITAR NOTA ========== -->
    <!-- HU02: CreaciÃƒÂ³n (12/05/2026) -->
    <!-- HU03: EdiciÃƒÂ³n y eliminaciÃƒÂ³n (13/05/2026) -->
    <!-- HU05: Editor enriquecido (15/05/2026) -->
    <div class="modal-overlay" id="modalOverlay">
        <div class="modal modal-editor">
            <div class="modal-header">
                <h2 class="modal-title" id="modalTitle">Nueva Nota</h2>
                <button class="btn-close-modal" id="btnCloseModal" title="Cerrar">Ã¢Å“â€¢</button>
            </div>

            <form id="noteForm">
                <!-- Campo oculto para identificar si es ediciÃƒÂ³n (HU03) -->
                <input type="hidden" id="noteId" name="idnota" value="">

                <!-- TÃƒÂ­tulo -->
                <div class="form-group">
                    <label class="form-label" for="noteTitle">TÃƒÂ­tulo de la nota</label>
                    <input
                        type="text"
                        class="form-input"
                        id="noteTitle"
                        name="titulo"
                        placeholder="Escribe el tÃƒÂ­tulo aquÃƒÂ­..."
                        required
                        autocomplete="off">
                </div>

                <!-- HU05: Editor enriquecido -->
                <div class="form-group">
                    <label class="form-label">Contenido</label>

                    <!-- Barra de herramientas del editor -->
                    <div class="editor-toolbar">
                        <button type="button" class="toolbar-btn" data-command="bold" title="Negrita (Ctrl+B)">
                            <strong>B</strong>
                        </button>
                        <button type="button" class="toolbar-btn" data-command="italic" title="Cursiva (Ctrl+I)">
                            <em>I</em>
                        </button>
                        <button type="button" class="toolbar-btn" data-command="underline" title="Subrayado (Ctrl+U)">
                            <u>U</u>
                        </button>
                        <span class="toolbar-separator"></span>
                        <button type="button" class="toolbar-btn" data-command="insertUnorderedList" title="Lista con viÃƒÂ±etas">
                            Ã¢â‚¬Â¢ Lista
                        </button>
                        <button type="button" class="toolbar-btn" data-command="insertOrderedList" title="Lista numerada">
                            1. Lista
                        </button>
                        <span class="toolbar-separator"></span>
                        <button type="button" class="toolbar-btn" data-command="formatBlock" data-value="h3" title="Encabezado grande">
                            H
                        </button>
                        <button type="button" class="toolbar-btn" data-command="formatBlock" data-value="h4" title="Encabezado mediano">
                            h
                        </button>
                        <span class="toolbar-separator"></span>
                        <button type="button" class="toolbar-btn" data-command="removeFormat" title="Limpiar formato">
                            Ã¢Å“â€¢
                        </button>
                    </div>

                    <!-- ÃƒÂrea de contenido editable (HU05) -->
                    <div
                        class="editor-content"
                        id="noteContent"
                        contenteditable="true"
                        placeholder="Escribe el contenido de la nota..."
                        role="textbox"
                        aria-multiline="true"></div>
                </div>

                <!-- Footer del modal -->
                <div class="modal-footer">
                    <!-- BotÃƒÂ³n eliminar solo visible en ediciÃƒÂ³n (HU03) -->
                    <button type="button" class="btn btn-danger" id="btnDeleteNote" style="display: none;">
                        Ã°Å¸â€”â€˜Ã¯Â¸Â Eliminar
                    </button>
                    <div class="modal-footer-right">
                        <button type="button" class="btn btn-secondary" id="btnCancelNote">
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary" id="btnSaveNote">
                            Guardar Nota
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- ========== MODAL CONFIRMAR ELIMINACIÃƒâ€œN (HU03) ========== -->
    <div class="modal-overlay" id="confirmDeleteOverlay">
        <div class="modal modal-confirm">
            <div class="modal-header">
                <h2 class="modal-title">Confirmar EliminaciÃƒÂ³n</h2>
                <button class="btn-close-modal" id="btnCloseConfirm">Ã¢Å“â€¢</button>
            </div>
            <p class="confirm-text">Ã‚Â¿EstÃƒÂ¡s seguro de que deseas eliminar esta nota?</p>
            <p class="confirm-warning">Esta acciÃƒÂ³n no se puede deshacer.</p>
            <div class="modal-footer">
                <button class="btn btn-secondary" id="btnCancelDelete">Cancelar</button>
                <button class="btn btn-danger" id="btnConfirmDelete">Eliminar</button>
            </div>
        </div>
    </div>

    <script src="app.js"></script>
</body>

</html>
