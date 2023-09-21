<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Agenda de Eventos</title>
    <link href="{{ asset('css.css') }}" rel="stylesheet">
    <script src="{{ asset('calendario.js') }}"></script>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>

    <script>
       
    </script>

    <script src="js/sweetalert2.all.min.js"></script>
    <link href="js/fullcalendar/lib/main.css" rel="stylesheet" />
    <script src="js/fullcalendar/lib/main.js"></script>


<body>
    <div class="container">
        <div class="table-wrapper">
            <div class="table-title">
                <div class="row">
                    <div class="col-sm-6">
                        <h2>Agenda de <b>Eventos</b></h2>
                    </div>
                    <div class="col-sm-6">
                        @auth
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-primary">Logout</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-info">Login</a>
                        @endauth
                        <a onclick="NewEvento()" class="btn btn-success" data-toggle="modal">
                            <i class="material-icons">&#xE147;</i> <span>Criar novo evento</span>
                        </a>
                        <a href="#deleteEmployeeModal" class="btn btn-danger" data-toggle="modal">
                            <i class="material-icons">&#xE15C;</i> <span>Remover</span>
                        </a>
                    </div>
                    <!-- <div class="col-sm-6">
                    @auth
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-primary">Logout</button>
                            </form>
@else
    <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
                    @endauth
                        <a href="#addEmployeeModal" class="btn btn-success" data-toggle="modal">
                            <i class="material-icons">&#xE147;</i> <span>Criar novo evento</span>
                        </a>
                        <a href="#deleteEmployeeModal" class="btn btn-danger" data-toggle="modal">
                            <i class="material-icons">&#xE15C;</i> <span>Remover</span>
                        </a>
                    </div> -->
                </div>
            </div>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>
                            <span class="custom-checkbox">
                                <input type="checkbox" id="selectAll">
                                <label for="selectAll"></label>
                            </span>
                        </th>
                        <th>Titulo</th>
                        <th>Sobre</th>
                        <th>Status</th>
                        <th>Data de Início</th>
                        <th>Data de Encerramento</th>
                        <th>Responsável</th>
                        <th>Ações</th>


                    </tr>
                </thead>


                <tbody id="eventosTable">


                </tbody>

            </table>

        </div>
    </div>
    <div class="container" id='calendar'></div>
    
</body>

</html>

<script>
    // Função que faz consumo e distribuição dos eventos

    $.ajax({
        type: 'GET',
        url: '/eventos',
        success: function(data) {
            console.log(data);
            var eventos = data.eventos;
            var tableBody = $('#eventosTable');
            $.each(eventos, function(index, evento) {
                var row = '<tr>' +
                    '<td><span class="custom-checkbox"><input type="checkbox" id="checkbox' + evento
                    .id + '" name="options[]" value="' + evento.id + '"><label for="checkbox' +
                    evento.id + '"></label></span></td>' +
                    '<td>' + evento.title + '</td>' +
                    '<td>' + evento.description + '</td>' +
                    '<td>' + evento.status + '</td>' +
                    '<td>' + evento.start + '</td>' +
                    '<td>' + evento.end + '</td>' +
                    '<td>' + evento.usr_responsavel + '</td>' +

                    '<td>' +
                    '<a href="#editEmployeeModal" class="edit" onclick="EditarEvento(this)" data-title="' +
                    evento.title +
                    '"  data-toggle="modal"><i class="material-icons" data-toggle="tooltip" title="Edit">&#xE254;</i></a>' +
                    '<a href="#deleteEmployeeModal" class="delete" onclick="RemoveEvento(this)" data-title="' +
                    evento.title +
                    '" ><i class="material-icons" data-toggle="tooltip" title="Delete">&#xE872;</i></a>'

                '</td>' +
                '</tr>';
                tableBody.append(row);
            });

        },
        error: function(error) {
            console.error('Erro na solicitação AJAX:', error);
        }
    });

    function RemoveEvento(x) {
        var title = $(x).data('title');
        var url = 'excluir-evento/' + encodeURIComponent(title);

        Swal.fire({
            title: 'Remover Evento',
            html: '<p>Tem certeza que deseja remover o evento? Essa ação não poderá ser desfeita.</p>',
            showCancelButton: true,
            confirmButtonText: 'Remover',
            cancelButtonText: 'Cancelar',
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'DELETE',
                    url: url,
                    success: function(response) {
                        Swal.fire({
                            title: 'Evento removido com sucesso',
                            icon: 'success',
                        }).then(function() {
                            location.reload(); // Recarrega a página após clicar em "OK"
                        });
                    },
                    error: function(error) {
                        console.error('Erro na solicitação AJAX:', error);
                        Swal.fire({
                            title: 'Erro ao remover evento',
                            text: 'Houve um erro ao remover o evento.',
                            icon: 'error',
                        });
                    }
                });
            }
        });
    }

    function NewEvento(x) {
        Swal.fire({
            title: 'Adicionar Evento',
            html: '<div class="custom-swal-modal" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 20px">' +
                '<input id="swalEvtTitle" class="swal2-input custom-swal-input" style="width: 100%;" placeholder="Digite o título">' +
                '<input id="swalEvtDesc" class="swal2-input custom-swal-input" style="width: 100%; margin-top: 10px" placeholder="Digite a descrição"></input>' +
                '<label for="swalEvtStartDate" class="custom-swal-title" style="width: 100%">Data Inicial:</label>' +
                '<input type="datetime-local" id="swalEvtStartDate" class="swal2-input" style="width: 100%">' +
                '<label for="swalEvtEndDate" class="custom-swal-title">Data Final:</label>' +
                '<input type="datetime-local" id="swalEvtEndDate" class="swal2-input" style="width: 100%">' +
                '</div>',
            showCancelButton: true,
            confirmButtonText: 'Adicionar',
            cancelButtonText: 'Cancelar',
            customClass: {
                popup: 'custom-swal-modal',
                title: 'custom-swal-title',
                confirmButton: 'custom-swal-confirm-button',
                cancelButton: 'custom-swal-cancel-button',
            },
            preConfirm: function() {
                var eventTitle = document.getElementById('swalEvtTitle').value;
                var eventDescription = document.getElementById('swalEvtDesc').value;
                var startDate = new Date(document.getElementById('swalEvtStartDate').value);
                var endDate = new Date(document.getElementById('swalEvtEndDate').value);

                // Formatar as datas no formato 'Y-m-d H:i:s'
                var formattedStartDate = startDate.toISOString().slice(0, 19).replace("T", " ");
                var formattedEndDate = endDate.toISOString().slice(0, 19).replace("T", " ");

                console.log(formattedStartDate);

                if (!eventTitle) {
                    Swal.showValidationMessage('O título do evento é obrigatório.');
                    return false;
                }

                if (startDate > endDate) {
                    Swal.showValidationMessage('A data final não pode ser anterior à data inicial.');
                    return false;
                }

                var eventData = {
                    title: eventTitle,
                    description: eventDescription,
                    status: 'Aberto',
                    start: formattedStartDate,
                    end: formattedEndDate
                };

                // Sending AJAX request
                fetch("/criar-evento", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(eventData)
                    })
                    .then(response => {
                        console.log("Resposta do servidor:", response);
                        console.log("Resposta do servidor:", eventData);
                        console.log("par:", response);
                        if (response.status == 201) {

                            Swal.fire('Evento adicionado com sucesso!', '', 'success');
                            location.reload();
                        }
                        if (response.status == 400) {

                            Swal.fire('Não é permitido registrar eventos em finais de semana', '',
                                'error');
                        }
                        if (response.status == 500) {

                            Swal.fire('Já existe um evento com a mesma data de início', '', 'error');
                        }
                    })

                return false;
            }
        }).then(function(result) {
            if (result.isConfirmed) {
                Swal.fire('Evento adicionado com sucesso!', '', 'success');
            }
        });


    }


    function EditarEvento(x) {
        var title = $(x).data('title'); // Obtém o título do atributo de dados
        var url = 'eventos/' + encodeURIComponent(title);

        $.ajax({
            type: 'GET',
            url: url,
            success: function(data) {
                console.log(data);
                var eventos = data.eventos;
                var dataInicioFormatada = formatarData(eventos.start);
                var dataPrazoFormatada = formatarData(eventos.end);

                var pai = document.createElement('div');
                pai.innerHTML = `<div class="modal-dialog">
                <div class="modal-content">
                    <form id="editEventoForm"> <!-- Adicione um ID ao formulário -->
                        @csrf <!-- Adicione o token CSRF para proteção contra ataques CSRF -->

                        <div class="modal-header">
                            <h4 class="modal-title">Editar Evento ` + eventos.title + `</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Título</label>
                                <input type="text" name="title" value='` + eventos.title + `' class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Descrição</label>
                                <input type="text" name="descricao" value='` + eventos.descricao + `' class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control value='` + eventos.status + `'">
                                    <option value=""></option>
                                    <option value="Concluido">Concluído</option>
                                    <option value="Andamento">Em Andamento</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Data de Início</label>
                                <input type="date" value="` + dataInicioFormatada + `" name="data_inicio" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Data de Encerramento</label>
                                <input type="date" name="data_prazo" value="` + dataPrazoFormatada + `" class="form-control" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
                            <input type="submit" class="btn btn-success" value="Adicionar">
                        </div>
                    </form>
                </div>
            </div>
        `;

                // Encontre a div com o ID editEmployeeModal
                var editModal = document.getElementById('editEmployeeModal');

                // Limpe qualquer conteúdo existente na div
                editModal.innerHTML = '';

                // Adicione o formulário à div
                editModal.appendChild(pai);
                $('#editEventoForm').submit(function(e) {
                    e.preventDefault(); // Impede o envio padrão do formulário

                    var formData = $(this).serialize(); // Serialize os dados do formulário
                    var url = 'editar-evento/' + encodeURIComponent(title);

                    $.ajax({
                        type: 'put',
                        url: url, // Substitua pelo URL correto da sua rota de criação de evento
                        data: formData,
                        success: function(response) {
                            // Manipule a resposta da API, se necessário
                            console.log(response);
                            // Feche o modal
                            $('#editEmployeeModal').modal('hide');
                        },
                        error: function(error) {
                            console.error('Erro na solicitação AJAX:', error);
                        }
                    });
                });
            },
            error: function(error) {
                console.error('Erro na solicitação AJAX:', error);
            }
        });


    }

    $('#addEventoForm').submit(function(e) {
        e.preventDefault(); // Impede o envio padrão do formulário

        var formData = $(this).serialize(); // Serialize os dados do formulário

        $.ajax({
            type: 'POST',
            url: 'criar-evento', // Substitua pelo URL correto da sua rota de criação de evento
            data: formData,
            success: function(response) {
                // Manipule a resposta da API, se necessário
                console.log(response);
                // Feche o modal
                $('#addEmployeeModal').modal('hide');
            },
            error: function(error) {
                console.error('Erro na solicitação AJAX:', error);
            }
        });
    });


    function formatarData(dataHoraStr) {
        var partes = dataHoraStr.split(' ')[0].split('-');
        if (partes.length === 3) {
            var ano = partes[0];
            var mes = partes[1];
            var dia = partes[2];
            return ano + '-' + mes + '-' + dia;
        }
        // Retorna a data original se não for possível formatar
        return dataHoraStr;
    }

    $('.delete').click(function(e) {
        e.preventDefault(); // Impede o comportamento padrão do link

        var title = $(this).data('title'); // Obtém o título do atributo de dados

        if (confirm('Tem certeza que deseja excluir o evento "' + title + '"?')) {
            var url = '/excluir-evento/' +
            title; // Substitua pelo URL correto da sua rota de exclusão de evento

            $.ajax({
                type: 'DELETE', // Use o método DELETE para solicitar exclusão
                url: url,
                success: function(response) {
                    // Manipule a resposta da API, se necessário
                    console.log(response);
                    // Recarregue a página ou atualize a lista de eventos, se necessário
                },
                error: function(error) {
                    console.error('Erro na solicitação AJAX:', error);
                }
            });
        }
    });
</script>






