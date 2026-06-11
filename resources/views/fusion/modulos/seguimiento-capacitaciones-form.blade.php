@extends('layouts.app')

@section('title', $registro ? 'Editar Capacitación' : 'Nueva Capacitación')

@section('css')
<style>
  :root {
    --primary-color: #3b82f6;
    --primary-hover: #2563eb;
    --bg-light: #f8fafc;
    --border-color: #e2e8f0;
    --text-main: #1e293b;
    --text-muted: #64748b;
    --card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
  }

  .form-header {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    color: #ffffff;
    border-radius: 16px;
    padding: 2.5rem;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
    box-shadow: var(--card-shadow);
  }

  .form-header::after {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(59, 130, 246, 0.2) 0%, transparent 70%);
    border-radius: 50%;
    pointer-events: none;
  }

  .form-card {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid var(--border-color);
    box-shadow: var(--card-shadow);
    padding: 2rem;
  }

  .form-group label {
    font-weight: 700;
    color: var(--text-main);
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
  }

  .form-control-custom {
    border-radius: 10px;
    border: 2px solid var(--border-color);
    padding: 0.75rem 1rem;
    font-size: 1rem;
    transition: all 0.2s;
    color: var(--text-main);
  }

  .form-control-custom:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
    outline: none;
  }

  /* Modal Wizard */
  .modal-wizard-header {
    background-color: var(--bg-light);
    border-bottom: 1px solid var(--border-color);
    padding: 1.5rem 2rem;
  }

  .wizard-modal-progress {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    width: 100%;
    margin-top: 1rem;
  }

  .wizard-modal-progress::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--border-color);
    transform: translateY(-50%);
    z-index: 1;
  }

  .wizard-modal-progress-bar {
    position: absolute;
    top: 50%;
    left: 0;
    width: 0%;
    height: 3px;
    background: var(--primary-color);
    transform: translateY(-50%);
    z-index: 2;
    transition: width 0.3s ease;
  }

  .wizard-modal-step {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: #ffffff;
    border: 2px solid var(--border-color);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.85rem;
    color: var(--text-muted);
    position: relative;
    z-index: 3;
    transition: all 0.3s ease;
  }

  .wizard-modal-step.active {
    border-color: var(--primary-color);
    color: var(--primary-color);
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
    background: #ffffff;
  }

  .wizard-modal-step.complete {
    background: var(--primary-color);
    border-color: var(--primary-color);
    color: #ffffff;
  }

  .wizard-modal-step-label {
    position: absolute;
    top: 38px;
    left: 50%;
    transform: translateX(-50%);
    font-size: 0.75rem;
    font-weight: 700;
    white-space: nowrap;
    color: var(--text-muted);
  }

  .wizard-modal-step.active .wizard-modal-step-label {
    color: var(--primary-color);
  }

  .wizard-modal-body {
    padding: 2rem;
    min-height: 320px;
  }

  /* Empty State Cursos */
  .empty-courses-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 3rem 1rem;
    color: var(--text-muted);
  }

  .empty-courses-icon {
    font-size: 4rem;
    color: #cbd5e1;
    margin-bottom: 1.5rem;
  }

  /* Courses List */
  .course-list-item {
    background: var(--bg-light);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 1.25rem;
    margin-bottom: 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: all 0.2s;
  }

  .course-list-item:hover {
    border-color: #cbd5e1;
    background: #f1f5f9;
  }

  .course-info {
    flex-grow: 1;
    padding-right: 1.5rem;
  }

  .course-title {
    font-size: 1.05rem;
    font-weight: 700;
    color: var(--text-main);
    margin-bottom: 0.25rem;
  }

  .course-desc {
    font-size: 0.9rem;
    color: var(--text-muted);
    margin-bottom: 0.5rem;
  }

  .course-stages-badge {
    background: #e2e8f0;
    color: var(--text-main);
    font-size: 0.75rem;
    font-weight: 700;
    padding: 0.25rem 0.6rem;
    border-radius: 12px;
  }

  /* Stage inputs */
  .stage-input-group {
    background: var(--bg-light);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 1.25rem;
    margin-bottom: 1rem;
    position: relative;
  }

  .stage-input-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
  }

  .stage-input-number {
    font-weight: 700;
    color: var(--text-muted);
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }

  .stage-delete-btn {
    background: none;
    border: none;
    color: #ef4444;
    cursor: pointer;
    font-size: 0.9rem;
    padding: 0;
    transition: opacity 0.2s;
  }

  .stage-delete-btn:hover {
    opacity: 0.8;
  }

  /* CSS Animations */
  @keyframes slideIn {
    from { opacity: 0; transform: translateY(15px); }
    to { opacity: 1; transform: translateY(0); }
  }

  .animate-slide {
    animation: slideIn 0.35s ease-out forwards;
  }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">

  {{-- Header --}}
  <div class="form-header animate-slide">
    <div class="row align-items-center">
      <div class="col-md-9">
        <h1 class="font-weight-bold mb-2">
          <i class="fas fa-graduation-cap mr-2 text-primary"></i>
          {{ $registro ? 'Editar Capacitación' : 'Crear Nueva Capacitación' }}
        </h1>
        <p class="mb-0 text-white-50" style="font-size: 1.1rem;">
          {{ $registro ? 'Modifica los datos principales y gestiona sus cursos formativos.' : 'Introduce los datos básicos y define la ruta de capacitación.' }}
        </p>
      </div>
      <div class="col-md-3 text-md-right mt-3 mt-md-0">
        <a href="{{ route('seguimiento.capacitaciones') }}" class="btn btn-light px-4 py-2 font-weight-bold shadow-sm" style="border-radius: 10px;">
          <i class="fas fa-arrow-left mr-2"></i>Volver al Listado
        </a>
      </div>
    </div>
  </div>

  {{-- Formulario Principal --}}
  <div class="form-card animate-slide">
    <form id="capacitacionForm" action="{{ $registro ? route('seguimiento.crud.update', ['seccion' => $seccion, 'id' => $registro->id_capacitacion]) : route('seguimiento.crud.store', ['seccion' => $seccion]) }}" method="POST">
      @csrf
      @if($registro)
        @method('PUT')
      @endif

      {{-- Hidden input for serialized courses JSON --}}
      <input type="hidden" name="cursos" id="cursos-serialized-input" value="">

      <div class="form-group mb-4">
        <label for="nombre">Nombre de la Capacitación</label>
        <input type="text" name="nombre" id="nombre" class="form-control form-control-custom" placeholder="Ej. Primeros Auxilios Forestales Avanzados" value="{{ old('nombre', $registro->nombre ?? '') }}" required>
      </div>

      <div class="form-group mb-4">
        <label for="descripcion">Descripción General</label>
        <textarea name="descripcion" id="descripcion" class="form-control form-control-custom" rows="4" placeholder="Describe los alcances y competencias que adquirirá el voluntario al culminar esta capacitación." required>{{ old('descripcion', $registro->descripcion ?? '') }}</textarea>
      </div>

      <hr class="my-4" style="border-color: var(--border-color);">

      {{-- Botones del Formulario arranged horizontally --}}
      <div class="d-flex justify-content-between align-items-center">
        <a href="{{ route('seguimiento.capacitaciones') }}" class="btn btn-outline-secondary px-4 py-3 font-weight-bold" style="border-radius: 10px;">
          Cancelar
        </a>

        <div class="d-flex" style="gap: 12px;">
          <button type="button" class="btn btn-info px-4 py-3 font-weight-bold shadow-sm" data-toggle="modal" data-target="#modalGestionarCursos" style="border-radius: 10px; background-color: #0d9488; border-color: #0d9488;">
            <i class="fas fa-list-ul mr-2"></i>Gestionar Cursos
            <span class="badge badge-light ml-2" id="cursos-count-badge" style="font-size: 0.85rem; border-radius: 50%; padding: 4px 8px;">0</span>
          </button>
          <button type="submit" class="btn btn-primary px-5 py-3 font-weight-bold shadow-sm" style="border-radius: 10px;">
            Guardar Capacitación
          </button>
        </div>
      </div>
    </form>
  </div>

</div>

{{-- MODAL "GESTIONAR CURSOS" WIZARD --}}
<div class="modal fade" id="modalGestionarCursos" tabindex="-1" role="dialog" aria-labelledby="modalGestionarCursosLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
      
      {{-- Modal Header with steps indicator --}}
      <div class="modal-wizard-header">
        <div class="d-flex justify-content-between align-items-center">
          <h5 class="modal-title font-weight-bold text-dark" id="modalGestionarCursosLabel">
            <i class="fas fa-book-open text-primary mr-2"></i>Gestionar Cursos
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="modal-close-cross">
            <span aria-hidden="true" style="font-size: 1.5rem;">&times;</span>
          </button>
        </div>

        {{-- Steps Progress Bar --}}
        <div class="wizard-modal-progress">
          <div class="wizard-modal-progress-bar" id="wizard-modal-bar"></div>
          <div class="wizard-modal-step active" id="modal-step-node-1">
            1
            <span class="wizard-modal-step-label">Lista de Cursos</span>
          </div>
          <div class="wizard-modal-step" id="modal-step-node-2">
            2
            <span class="wizard-modal-step-label">Datos del Curso</span>
          </div>
          <div class="wizard-modal-step" id="modal-step-node-3">
            3
            <span class="wizard-modal-step-label">Ver Etapas</span>
          </div>
        </div>
      </div>

      {{-- Modal Body containing Steps --}}
      <div class="wizard-modal-body">
        
        {{-- PASO 1: LISTA DE CURSOS --}}
        <div id="modal-step-1">
          <div id="courses-list-container">
            {{-- Dynamically populated --}}
          </div>
          
          <div class="empty-courses-state d-none" id="empty-courses-view">
            <i class="fas fa-folder-open empty-courses-icon"></i>
            <h5 class="font-weight-bold text-dark mb-2">No hay cursos registrados</h5>
            <p class="text-muted mb-4" style="max-width: 400px;">
              Agrega los cursos teóricos o prácticos requeridos para obtener la acreditación de esta capacitación.
            </p>
            <button type="button" class="btn btn-primary px-4 py-2 font-weight-bold" id="btn-add-first-course" style="border-radius: 8px;">
              <i class="fas fa-plus mr-2"></i>Agregar Primer Curso
            </button>
          </div>

          <div class="d-flex justify-content-end mt-4 pt-2" id="btn-add-course-container">
            <button type="button" class="btn btn-outline-primary px-4 py-2 font-weight-bold" id="btn-add-new-course" style="border-radius: 8px;">
              <i class="fas fa-plus mr-2"></i>Agregar Otro Curso
            </button>
          </div>
        </div>

        {{-- PASO 2: DATOS DEL CURSO --}}
        <div id="modal-step-2" class="step-hidden">
          <div class="form-group mb-4">
            <label for="modal-course-name">Nombre del Curso</label>
            <input type="text" id="modal-course-name" class="form-control form-control-custom" placeholder="Ej. Introducción al Control de Fuego">
          </div>
          <div class="form-group mb-4">
            <label for="modal-course-desc">Descripción del Curso</label>
            <textarea id="modal-course-desc" class="form-control form-control-custom" rows="4" placeholder="Detalla los objetivos principales de este nivel."></textarea>
          </div>
        </div>

        {{-- PASO 3: VER ETAPAS (MIN 3 REQUIRED) --}}
        <div id="modal-step-3" class="step-hidden">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="font-weight-bold text-dark mb-0">Etapas del Curso (Mínimo 3)</h6>
            <button type="button" class="btn btn-xs btn-outline-info font-weight-bold" id="btn-add-stage" style="border-radius: 6px; padding: 4px 10px;">
              <i class="fas fa-plus mr-1"></i>Añadir Etapa
            </button>
          </div>
          <div id="stages-container">
            {{-- Stage inputs generated dynamically --}}
          </div>
        </div>

      </div>

      {{-- Modal Footer --}}
      <div class="modal-footer bg-light" style="border-top: 1px solid var(--border-color); border-bottom-left-radius: 20px; border-bottom-right-radius: 20px;">
        <button type="button" class="btn btn-secondary px-4 py-2" data-dismiss="modal" id="btn-modal-close" style="border-radius: 8px;">Cerrar</button>
        
        <div class="d-flex" style="gap: 8px;">
          <button type="button" class="btn btn-outline-secondary px-4 py-2 font-weight-bold step-hidden" id="btn-modal-back" style="border-radius: 8px;">
            Atrás
          </button>
          <button type="button" class="btn btn-primary px-4 py-2 font-weight-bold step-hidden" id="btn-modal-next" style="border-radius: 8px;">
            Siguiente
          </button>
          <button type="button" class="btn btn-success px-4 py-2 font-weight-bold step-hidden" id="btn-modal-save-course" style="border-radius: 8px;">
            Guardar Curso
          </button>
        </div>
      </div>

    </div>
  </div>
</div>
@endsection

@section('js')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    // Carga inicial de cursos desde el registro (si estamos editando)
    // El controlador pasa el registro. cursos es un jsonb en Postgres.
    @php
      $rawCursos = '[]';
      if ($registro && isset($registro->cursos)) {
          if (is_array($registro->cursos)) {
              $rawCursos = json_encode($registro->cursos);
          } elseif (is_string($registro->cursos)) {
              $rawCursos = $registro->cursos;
          }
      }
    @endphp
    
    let cursos = [];
    try {
      cursos = JSON.parse({!! json_encode($rawCursos) !!}) || [];
      // Si cursos se decodificó como un string con JSON adentro por doble codificación:
      if (typeof cursos === 'string') {
        cursos = JSON.parse(cursos) || [];
      }
    } catch (e) {
      console.error("Error decodificando cursos:", e);
      cursos = [];
    }

    // Inputs ocultos y badges del form principal
    const formInput = document.getElementById('cursos-serialized-input');
    const badgeCount = document.getElementById('cursos-count-badge');
    const mainForm = document.getElementById('capacitacionForm');

    function syncMainForm() {
      formInput.value = JSON.stringify(cursos);
      badgeCount.textContent = cursos.length;
    }
    syncMainForm();

    // Modal Wizard Variables
    let currentModalStep = 1;
    let editingCourseIndex = null; // null si es nuevo curso, integer si se está editando
    
    // Objeto temporal de curso activo
    let tempCourse = {
      nombre: '',
      desc: '',
      etapas: []
    };

    const modalStep1 = document.getElementById('modal-step-1');
    const modalStep2 = document.getElementById('modal-step-2');
    const modalStep3 = document.getElementById('modal-step-3');

    const bar = document.getElementById('wizard-modal-bar');
    const node1 = document.getElementById('modal-step-node-1');
    const node2 = document.getElementById('modal-step-node-2');
    const node3 = document.getElementById('modal-step-node-3');

    const btnClose = document.getElementById('btn-modal-close');
    const btnCross = document.getElementById('modal-close-cross');
    const btnBack = document.getElementById('btn-modal-back');
    const btnNext = document.getElementById('btn-modal-next');
    const btnSaveCourse = document.getElementById('btn-modal-save-course');

    const btnAddFirst = document.getElementById('btn-add-first-course');
    const btnAddNew = document.getElementById('btn-add-new-course');
    const btnAddStage = document.getElementById('btn-add-stage');

    const modalCourseName = document.getElementById('modal-course-name');
    const modalCourseDesc = document.getElementById('modal-course-desc');

    const containerList = document.getElementById('courses-list-container');
    const viewEmpty = document.getElementById('empty-courses-view');
    const containerBtnAdd = document.getElementById('btn-add-course-container');
    const containerStages = document.getElementById('stages-container');

    // Renderiza la lista de cursos en el paso 1 del modal
    function renderCoursesList() {
      containerList.innerHTML = '';
      if (cursos.length === 0) {
        viewEmpty.classList.remove('d-none');
        containerBtnAdd.classList.add('d-none');
      } else {
        viewEmpty.classList.add('d-none');
        containerBtnAdd.classList.remove('d-none');

        cursos.forEach((c, index) => {
          const item = document.createElement('div');
          item.className = 'course-list-item animate-slide';
          item.innerHTML = `
            <div class="course-info">
              <div class="course-title">${escapeHtml(c.nombre)}</div>
              <div class="course-desc">${escapeHtml(c.desc || 'Sin descripción.')}</div>
              <span class="course-stages-badge">
                <i class="fas fa-tasks mr-1"></i> ${c.etapas ? c.etapas.length : 0} Etapas
              </span>
            </div>
            <div class="action-btn-group">
              <button type="button" class="action-btn action-btn-edit btn-edit-course-item" data-index="${index}">
                <i class="fas fa-pencil-alt"></i>
              </button>
              <button type="button" class="action-btn action-btn-delete btn-delete-course-item" data-index="${index}">
                <i class="fas fa-trash-alt"></i>
              </button>
            </div>
          `;
          containerList.appendChild(item);
        });

        // Event listeners para editar y eliminar
        document.querySelectorAll('.btn-edit-course-item').forEach(btn => {
          btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const idx = parseInt(btn.getAttribute('data-index'));
            startEditCourse(idx);
          });
        });

        document.querySelectorAll('.btn-delete-course-item').forEach(btn => {
          btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const idx = parseInt(btn.getAttribute('data-index'));
            if (confirm(`¿Seguro que deseas eliminar el curso "${cursos[idx].nombre}"?`)) {
              cursos.splice(idx, 1);
              syncMainForm();
              renderCoursesList();
            }
          });
        });
      }
    }

    // Al abrir el modal, renderizamos
    $('#modalGestionarCursos').on('show.bs.modal', function () {
      goToWizardStep(1);
      renderCoursesList();
    });

    // Cambiar entre pasos del modal wizard
    function goToWizardStep(step) {
      currentModalStep = step;

      // Ocultar todas las secciones
      modalStep1.classList.add('step-hidden');
      modalStep2.classList.add('step-hidden');
      modalStep3.classList.add('step-hidden');

      // Ocultar todos los botones de control
      btnBack.classList.add('step-hidden');
      btnNext.classList.add('step-hidden');
      btnSaveCourse.classList.add('step-hidden');

      // Limpiar clases activas y completas en nodos
      node1.classList.remove('active', 'complete');
      node2.classList.remove('active', 'complete');
      node3.classList.remove('active', 'complete');

      if (step === 1) {
        modalStep1.classList.remove('step-hidden');
        bar.style.width = '0%';
        node1.classList.add('active');
        // Mostrar cerrar, ocultar atrás/siguiente
        btnClose.classList.remove('d-none');
        btnCross.style.display = '';
      } 
      else if (step === 2) {
        modalStep2.classList.remove('step-hidden');
        bar.style.width = '50%';
        node1.classList.add('complete');
        node2.classList.add('active');

        // Mostrar controles
        btnBack.classList.remove('step-hidden');
        btnNext.classList.remove('step-hidden');
        btnClose.classList.add('d-none');
        btnCross.style.display = 'none';
      } 
      else if (step === 3) {
        modalStep3.classList.remove('step-hidden');
        bar.style.width = '100%';
        node1.classList.add('complete');
        node2.classList.add('complete');
        node3.classList.add('active');

        // Mostrar controles
        btnBack.classList.remove('step-hidden');
        btnSaveCourse.classList.remove('step-hidden');
        btnClose.classList.add('d-none');
        btnCross.style.display = 'none';
      }
    }

    // Iniciar creación de nuevo curso
    function startNewCourse() {
      editingCourseIndex = null;
      tempCourse = {
        nombre: '',
        desc: '',
        etapas: ['', '', ''] // Minimo 3 etapas obligatorias
      };
      
      modalCourseName.value = '';
      modalCourseDesc.value = '';
      
      renderStageInputs();
      goToWizardStep(2);
    }

    // Iniciar edición de curso existente
    function startEditCourse(idx) {
      editingCourseIndex = idx;
      const original = cursos[idx];
      tempCourse = {
        nombre: original.nombre,
        desc: original.desc || '',
        etapas: original.etapas ? [...original.etapas] : ['', '', '']
      };

      // Si tiene menos de 3 etapas, rellenar hasta llegar a 3
      while (tempCourse.etapas.length < 3) {
        tempCourse.etapas.push('');
      }

      modalCourseName.value = tempCourse.nombre;
      modalCourseDesc.value = tempCourse.desc;

      renderStageInputs();
      goToWizardStep(2);
    }

    // Renderiza las etapas dinámicamente en el paso 3
    function renderStageInputs() {
      containerStages.innerHTML = '';
      tempCourse.etapas.forEach((val, idx) => {
        const div = document.createElement('div');
        div.className = 'stage-input-group animate-slide';
        
        // El botón de eliminar solo se muestra si hay más de 3 etapas
        const showDelete = tempCourse.etapas.length > 3;

        div.innerHTML = `
          <div class="stage-input-header">
            <span class="stage-input-number">Etapa ${idx + 1}</span>
            ${showDelete ? `<button type="button" class="stage-delete-btn btn-delete-stage" data-index="${idx}"><i class="fas fa-trash-alt mr-1"></i>Eliminar</button>` : ''}
          </div>
          <input type="text" class="form-control form-control-custom modal-stage-val" value="${escapeHtml(val)}" placeholder="Ej. Examen teórico inicial" data-index="${idx}" required>
        `;
        containerStages.appendChild(div);
      });

      // Añadir listeners para borrar etapas
      document.querySelectorAll('.btn-delete-stage').forEach(btn => {
        btn.addEventListener('click', () => {
          const idx = parseInt(btn.getAttribute('data-index'));
          saveStagesState(); // Guardar primero el estado actual de los inputs
          tempCourse.etapas.splice(idx, 1);
          renderStageInputs();
        });
      });
    }

    // Guarda el texto ingresado en los inputs de etapas en el objeto temporal
    function saveStagesState() {
      const inputs = document.querySelectorAll('.modal-stage-val');
      inputs.forEach(input => {
        const idx = parseInt(input.getAttribute('data-index'));
        if (tempCourse.etapas[idx] !== undefined) {
          tempCourse.etapas[idx] = input.value;
        }
      });
    }

    // Botón Agregar etapa
    btnAddStage.addEventListener('click', () => {
      saveStagesState();
      tempCourse.etapas.push('');
      renderStageInputs();
    });

    // Event listeners para agregar curso
    btnAddFirst.addEventListener('click', startNewCourse);
    btnAddNew.addEventListener('click', startNewCourse);

    // Botones de navegación del Wizard
    btnBack.addEventListener('click', () => {
      if (currentModalStep === 2) {
        goToWizardStep(1);
      } else if (currentModalStep === 3) {
        saveStagesState();
        goToWizardStep(2);
      }
    });

    btnNext.addEventListener('click', () => {
      if (currentModalStep === 2) {
        // Validar Paso 2
        const nameVal = modalCourseName.value.trim();
        const descVal = modalCourseDesc.value.trim();

        if (!nameVal || !descVal) {
          alert('Por favor ingresa el nombre y la descripción del curso.');
          return;
        }

        tempCourse.nombre = nameVal;
        tempCourse.desc = descVal;

        goToWizardStep(3);
      }
    });

    // Guardar Curso final en el wizard (Paso 3)
    btnSaveCourse.addEventListener('click', () => {
      saveStagesState();

      // Validar etapas
      let valid = true;
      let emptyCount = 0;
      const filteredEtapas = tempCourse.etapas.map(et => et.trim()).filter(et => {
        if (!et) emptyCount++;
        return et.length > 0;
      });

      if (filteredEtapas.length < 3) {
        alert('Debes registrar un mínimo de 3 etapas válidas completas.');
        return;
      }

      tempCourse.etapas = filteredEtapas;

      if (editingCourseIndex !== null) {
        // Editando
        cursos[editingCourseIndex] = { ...tempCourse };
      } else {
        // Nuevo
        cursos.push({ ...tempCourse });
      }

      syncMainForm();
      goToWizardStep(1);
      renderCoursesList();
    });

    // Validar y serializar al enviar formulario principal
    mainForm.addEventListener('submit', (e) => {
      syncMainForm();
      if (cursos.length === 0) {
        e.preventDefault();
        alert('Debes agregar al menos un curso a la capacitación.');
      }
    });

    // Helper functions
    function escapeHtml(text) {
      if (!text) return '';
      return text
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
    }
  });
</script>
@endsection
