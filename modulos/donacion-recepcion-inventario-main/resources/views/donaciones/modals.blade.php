<!-- Modal Create Product -->
<div class="modal fade" id="createProductModal" tabindex="-1" role="dialog" aria-labelledby="createProductModalLabel"
    aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title" id="createProductModalLabel">Crear Nuevo Producto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="quickProductForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="modal_id_categoria">
                            Categoría <span class="text-danger">*</span>
                            <button type="button" class="btn btn-xs btn-primary ml-1" id="btnOpenCategoryModal"
                                title="Crear nueva categoría">
                                <i class="fas fa-plus"></i>
                            </button>
                        </label>
                        <select name="id_categoria" id="modal_id_categoria" class="form-control" required>
                            <option value="">-- Seleccione --</option>
                            @foreach($categorias ?? [] as $catId => $catName)
                                <option value="{{ $catId }}">{{ $catName }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="modal_nombre">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" id="modal_nombre" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="modal_descripcion">Descripción</label>
                        <input type="text" name="descripcion" id="modal_descripcion" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="modal_unidad_medida">Unidad de Medida <span class="text-danger">*</span></label>
                        <input type="text" name="unidad_medida" id="modal_unidad_medida" class="form-control" required
                            placeholder="Ej: unidad, kg, litro">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar Producto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Create Category -->
<div class="modal fade" id="createCategoryModal" tabindex="-1" role="dialog" aria-labelledby="createCategoryModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="createCategoryModalLabel">Crear Nueva Categoría</h5>
                <button type="button" class="close" id="closeCategoryModal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="quickCategoryForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="category_nombre">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" id="category_nombre" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="category_codigo">Código (CAT-…)</label>
                        <input type="text" name="codigo" id="category_codigo" class="form-control text-uppercase" maxlength="24" placeholder="CAT-OTRO">
                    </div>
                    <div class="form-group">
                        <label for="category_tipo">Tipo</label>
                        <select name="tipo_categoria" id="category_tipo" class="form-control">
                            @foreach (\Modules\Inventario\Models\CategoriasProducto::TIPOS_CATEGORIA as $value => $label)
                                <option value="{{ $value }}" @selected($value === 'OTRO')>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="category_prioridad">Prioridad</label>
                        <select name="prioridad" id="category_prioridad" class="form-control">
                            @foreach (\Modules\Inventario\Models\CategoriasProducto::PRIORIDADES as $value => $label)
                                <option value="{{ $value }}" @selected($value === 'media')>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="category_descripcion">Descripción</label>
                        <textarea name="descripcion" id="category_descripcion" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="cancelCategoryModal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Categoría</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Create Donor -->
<div class="modal fade" id="createDonorModal" tabindex="-1" role="dialog" aria-labelledby="createDonorModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title" id="createDonorModalLabel">Crear Nuevo Donante</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="quickDonorForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="donor_nombre">Nombre <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" id="donor_nombre" class="form-control" required
                                    maxlength="150">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="donor_tipo">Tipo <span class="text-danger">*</span></label>
                                <select name="tipo" id="donor_tipo" class="form-control" required>
                                    <option value="">Seleccione un tipo</option>
                                    <option value="persona">Persona</option>
                                    <option value="empresa">Empresa</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="donor_email">Email</label>
                                <input type="email" name="email" id="donor_email" class="form-control" maxlength="100">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="donor_telefono">Teléfono</label>
                                <input type="text" name="telefono" id="donor_telefono" class="form-control"
                                    maxlength="20">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="donor_password">Contraseña <span class="text-danger">*</span></label>
                                <input type="password" name="password" id="donor_password" class="form-control" required
                                    minlength="6">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="donor_direccion">Dirección</label>
                                <textarea name="direccion" id="donor_direccion" class="form-control"
                                    rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-info">Guardar Donante</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Create Campaign -->
<div class="modal fade" id="createCampaignModal" tabindex="-1" role="dialog" aria-labelledby="createCampaignModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="createCampaignModalLabel">Crear Nueva Campaña</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="quickCampaignForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="campaign_nombre">Nombre de la Campaña <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" id="campaign_nombre" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="campaign_descripcion">Descripción <span class="text-danger">*</span></label>
                        <textarea name="descripcion" id="campaign_descripcion" class="form-control" rows="3"
                            required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="campaign_fecha_inicio">Fecha de Inicio <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="fecha_inicio" id="campaign_fecha_inicio" class="form-control"
                                    required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="campaign_fecha_fin">Fecha de Fin <span class="text-danger">*</span></label>
                                <input type="date" name="fecha_fin" id="campaign_fecha_fin" class="form-control"
                                    required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">Guardar Campaña</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Create Collection Point -->
<div class="modal fade" id="createCollectionPointModal" tabindex="-1" role="dialog"
    aria-labelledby="createCollectionPointModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-secondary">
                <h5 class="modal-title" id="createCollectionPointModalLabel">Crear Nuevo Punto de Recolección</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="quickCollectionPointForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="point_nombre">Nombre del Punto <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" id="point_nombre" class="form-control" required
                                    placeholder="Ej: Sede Central">
                            </div>
                            <div class="form-group">
                                <label for="point_direccion">Dirección <span class="text-danger">*</span></label>
                                <input type="text" name="direccion" id="point_direccion" class="form-control" required
                                    placeholder="Ej: Av. Principal #123">
                            </div>
                            <div class="form-group">
                                <label for="point_contacto">Contacto</label>
                                <input type="text" name="contacto" id="point_contacto" class="form-control"
                                    placeholder="Ej: Juan Pérez - 77712345">
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="point_latitud">Latitud</label>
                                        <input type="text" name="latitud" id="point_latitud" class="form-control"
                                            readonly>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="point_longitud">Longitud</label>
                                        <input type="text" name="longitud" id="point_longitud" class="form-control"
                                            readonly>
                                    </div>
                                </div>
                            </div>
                            <small class="text-muted"><i class="fas fa-map-marker-alt"></i> Haga clic en el mapa para
                                seleccionar la ubicación.</small>
                        </div>
                        <div class="col-md-6">
                            <label>Ubicación en el Mapa</label>
                            <div id="pointMap"
                                style="height: 350px; width: 100%; border: 1px solid #ccc; border-radius: 4px;"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-secondary">Guardar Punto</button>
                </div>
            </form>
        </div>
    </div>
</div>




