@extends('layouts.core')
@section('contenido')
    <div class="container-fluid">
        <!-- Page-Title -->
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <h4 class="page-title">Crear numeración</h4>
                </div>
                <!--end page-title-box-->
            </div>
            <!--end col-->
        </div>
        <!-- end page title end breadcrumb -->
        <div class="card">
            <div class="card-body">
                <form class="needs-validation" enctype="multipart/form-data" novalidate id="nuevaNumeracion" method="post">
                    @csrf
                    <div class="form-row">
                        <div class="col-md-4 form-group">
                            <label for="validationCustom01">Descripción</label>
                            <input type="text" class="form-control" name="descripcion" id="descripcion">
                        </div>
                        <div class="col-md-2 form-group">
                            <label for="">Tipo de serie</label>
                            <select name="tipo_serie" id="tipo_serie" class="form-control" required>
                                <option value="">Seleccione</option>
                                <option value="Cliente">Cliente</option>
                                <option value="Productos">Productos</option>
                                <option value="Albaranes">Albaranes</option>
                                <option value="Facturas">Facturas</option>
                                <option value="Presupuestos">Presupuestos</option>
                            </select>
                            <div class="invalid-feedback">
                                ¡El tipo de serie es obligatorio!
                            </div>
                        </div>
                        <div class="col-md-2 form-group">
                            <label for="validationCustom01">Identificador</label>
                            <input type="text" class="form-control" name="identificador" id="identificador" required>
                            <div class="invalid-feedback">
                                ¡El identificador es obligatorio!
                            </div>
                        </div>
                        <div class="col-md-2 form-group">
                            <label for="validationCustom01">Siguiente número</label>
                            <input type="text" class="form-control" name="siguiente_numero" id="siguiente_numero"
                                value="1" required>
                            <div class="invalid-feedback">
                                ¡El siguiente número es obligatorio!
                            </div>
                        </div>
                        <div class="col-md-2 form-group">
                            <label for="validationCustom01">Número de dígitos</label>
                            <input type="number" pattern="^[0-30]+" class="form-control" name="numero_digitos"
                                id="numero_digitos" value="1" required>
                            <div class="invalid-feedback">
                                ¡El número de dígitos es obligatorio!
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="px-4 pt-2">
                                    <h5 class="mb-0">Observaciones</h5>
                                </div>
                                <div class="card-body">
                                    <input type="hidden" name="observaciones" id="observaciones_hidden">
                                    <textarea id="observaciones" cols="30" rows="10"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="checkbox my-2">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="favorito" name="favorito"
                                    value="1">
                                <label class="custom-control-label" for="favorito">Favorito</label>
                            </div>
                        </div>
                        <div class="checkbox my-2">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="rellenar_con_ceros"
                                    name="rellenar_con_ceros" value="1">
                                <label class="custom-control-label" for="rellenar_con_ceros">Rellenar con ceros</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-12 text-center">
                            <button class="btn btn-primary" id="registrarNumeración" type="submit">Crear
                                numeración</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div><!-- container -->
@endsection
@section('add-scripts')
    <script src="{{ asset('plugins/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('plugins/tinymce/langs/es.js') }}"></script>
    <script>
        $(document).ready(function() {
            var configEditor = {
                theme: "modern",
                height: 150,
                plugins: [
                    "advlist autolink link preview hr pagebreak",
                    "save directionality paste textcolor "
                ],
                toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link preview | forecolor backcolor emoticons ",
                style_formats: [{
                        title: 'Bold text',
                        inline: 'b'
                    },
                    {
                        title: 'Red text',
                        inline: 'span',
                        styles: {
                            color: '#ff0000'
                        }
                    },
                    {
                        title: 'Red header',
                        block: 'h1',
                        styles: {
                            color: '#ff0000'
                        }
                    },
                    {
                        title: 'Example 1',
                        inline: 'span',
                        classes: 'example1'
                    },
                    {
                        title: 'Example 2',
                        inline: 'span',
                        classes: 'example2'
                    },
                ]
            };
            if ($("#observaciones").length > 0) {
                tinymce.init({
                    ...configEditor,
                    language: "es",
                    selector: "textarea#observaciones",
                });
            }
        });
        $("#nuevaNumeracion").submit(function(e) {
            e.preventDefault();
            $('#nuevaNumeracion').addClass('was-validated');
            if ($('#nuevaNumeracion')[0].checkValidity() === false) {
                event.stopPropagation();
            } else {
                $("#observaciones_hidden").val(tinymce.get('observaciones').getContent());
                $.ajax({
                    type: "post",
                    url: "/numeraciones",
                    data: new FormData($('#nuevaNumeracion')[0]),
                    contentType: false,
                    cache: false,
                    processData: false,
                    beforeSend: function() {
                        $("#registrarNumeracion").html(
                            "<i class='fa fa-spinner fa-pulse'></i><span class='sr-only'> Registrando...</span>Registrando..."
                        );
                        $("#registrarNumeracion").attr("disabled", true);
                    },
                    dataType: "json",
                    success: function(data) {
                        $("#registrarNumeracion").html("Registrado");
                        $("#registrarNumeracion").removeAttr("disabled");
                        if (data.status == "success") {
                            $('#nuevaNumeracion').removeClass('was-validated');
                            location.href = "/numeraciones";
                        } else if (data.status == "error") {
                            $('#nuevaNumeracion').removeClass('was-validated');
                            Swal.fire({
                                icon: data.status,
                                title: data.title,
                                text: data.message,
                            });
                        }
                    }
                });
            }
        });
    </script>
@endsection
