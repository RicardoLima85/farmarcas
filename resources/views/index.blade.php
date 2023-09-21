<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Agenda de Eventos</title>
<link href="{{ asset('./css/calendar.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round">
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  var calendarEl = document.getElementById('calendar');

  var calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    height: 650,
    events: 'fetchEvents.php',
    businessHours: {
      daysOfWeek: [1, 2, 3, 4, 5],
    },
    dateClick: function(info) {

      var selectedDayOfWeek = info.date.getDay();
      if (calendar.getOption('businessHours').daysOfWeek.includes(selectedDayOfWeek)) {
        Swal.fire({
          title: 'Adicionar Evento',
          html:
            '<div class="custom-swal-modal" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 20px">' +
            '<input id="swalEvtTitle" class="swal2-input custom-swal-input" style="width: 100%;" placeholder="Digite o título">' +
            '<input id="swalEvtDesc" class="swal2-input custom-swal-input" style="width: 100%; margin-top: 10px" placeholder="Digite a descrição"></input>' +
            '<label for="swalEvtStartDate" class="custom-swal-title" style="width: 100%">Data Inicial:</label>' +
            '<input type="datetime-local" id="swalEvtStartDate" class="swal2-input" style="width: 100%" value="' + info.dateStr.replace(/T.*$/, '') + 'T00:00">' +
            '<label for="swalEvtEndDate" class="custom-swal-title">Data Final:</label>' +
            '<input type="datetime-local" id="swalEvtEndDate" class="swal2-input" style="width: 100%" value="' + info.dateStr.replace(/T.*$/, '') + 'T23:59">' +
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
                if (response.status = 201) {
                    calendar.addEvent(eventData);
                    Swal.fire('Evento adicionado com sucesso!', '', 'success');
                } else {
                    Swal.fire('Erro ao adicionar evento', data.message, 'error');
                }

            })
           
            return false;
        }
        }).then(function(result) {
          if (result.isConfirmed) {
            Swal.fire('Evento adicionado com sucesso!', '', 'success');
          }
        });
      } else {
        alert('Esse dia não está disponível para agendamento.');
      }
    }
  });

  $.ajax({
                type: 'GET',
                url: '/eventos',
                success: function (data) {
                    console.log(data);
                    var eventos = data.eventos;
                    var tableBody = $('#eventosTable');
                    $.each(eventos, function(index, evento) {

                        var eventDataGET = {
                            title: evento.title,
                            description: evento.description,
                            status: 'Aberto',
                            start: evento.start,
                            end: evento.end};
                            calendar.addEvent(eventDataGET);

                            console.log(eventDataGET);

                    });

        },
        error: function (error) {
            console.error('Erro na solicitação AJAX:', error);
        }
    });

  calendar.render();
});
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
                        <a href="#addEmployeeModal" class="btn btn-success" data-toggle="modal">
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

                    </tr>
                </thead>


                <tbody id="eventosTable">


                </tbody>

            </table>

        </div>
    </div>
    <div class="container"  id='calendar'></div>
	<!-- Edit Modal HTML -->
    <!-- Edit Modal HTML -->
    <div id="addEmployeeModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <h3>calendar</h3>
                <form id="addEventoForm"> <!-- Adicione um ID ao formulário -->
                @csrf <!-- Adicione o token CSRF para proteção contra ataques CSRF -->

                <div class="modal-header">
                    <h4 class="modal-title">Adicionar Evento</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Título</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Descrição</label>
                        <input type="text" name="descricao" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="Aberto">Aberto</option>
                            <option value="Concluido">Concluído</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Data de Início</label>
                        <input type="date" name="data_inicio" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Data de Encerramento</label>
                        <input type="date" name="data_prazo" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
                    <input type="submit" class="btn btn-success" value="Adicionar">
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Edit Modal HTML -->

<!-- Edit Modal HTML -->
<div id="editEmployeeModal" class="modal fade">

</div>
	<!-- Delete Modal HTML -->
	<!-- Delete Modal HTML -->
<div id="deleteEmployeeModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="deleteEventoForm"> <!-- Adicione um ID ao formulário -->
                @csrf <!-- Adicione o token CSRF para proteção contra ataques CSRF -->

                <div class="modal-header">
                    <h4 class="modal-title">Remover Evento</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Tem certeza que deseja remover o evento da lista?</p>
                    <p class="text-warning"><small>Essa ação não poderá ser revertida.</small></p>
                </div>
                <div class="modal-footer">
                    <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
                    <input type="submit" class="btn btn-danger" value="Remover">
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Delete Modal HTML -->
</body>
</html>

<script>

    // Função que faz consumo e distribuição dos eventos

    $.ajax({
        type: 'GET',
        url: '/eventos',
        success: function (data) {
            console.log(data);
            var eventos = data.eventos;
            var tableBody = $('#eventosTable');
            $.each(eventos, function(index, evento) {
                var row = '<tr>' +
                    '<td><span class="custom-checkbox"><input type="checkbox" id="checkbox' + evento.id + '" name="options[]" value="' + evento.id + '"><label for="checkbox' + evento.id + '"></label></span></td>' +
                    '<td>' + evento.title + '</td>' +
                    '<td>' + evento.description + '</td>' +
                    '<td>' + evento.status + '</td>' +
                    '<td>' + evento.start + '</td>' +
                    '<td>' + evento.end + '</td>' +
                    '<td>' +
                        '<a href="#editEmployeeModal" class="edit" onclick="EditarEvento(this)" data-title="' + evento.title + '"  data-toggle="modal"><i class="material-icons" data-toggle="tooltip" title="Edit">&#xE254;</i></a>' +
                        '<a href="#deleteEmployeeModal" class="delete" onclick="RemoveEvento(this)" data-title="' + evento.title + '" ><i class="material-icons" data-toggle="tooltip" title="Delete">&#xE872;</i></a>'

                    '</td>' +
                '</tr>';
                tableBody.append(row);
            });

        },
        error: function (error) {
            console.error('Erro na solicitação AJAX:', error);
        }
    });

    function RemoveEvento(x){
        var title = $(x).data('title');
        var url = 'excluir-evento/' + encodeURIComponent(title);
        if (confirm('Tem certeza que deseja excluir o evento "' + title + '"?')) {


        $.ajax({
                    type: 'DELETE', // Use o método DELETE para solicitar exclusão
                    url: url,
                    success: function (response) {
                        // Manipule a resposta da API, se necessário
                        console.log(response);
                        // Recarregue a página ou atualize a lista de eventos, se necessário
                    },
                    error: function (error) {
                        console.error('Erro na solicitação AJAX:', error);
                    }
                });
            }
    }

    function EditarEvento(x){
        var title = $(x).data('title'); // Obtém o título do atributo de dados
        var url = 'eventos/' + encodeURIComponent(title);

        $.ajax({
        type: 'GET',
        url: url,
        success: function (data) {
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
                            <h4 class="modal-title">Editar Evento `+eventos.title+`</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Título</label>
                                <input type="text" name="title" value='`+eventos.title+`' class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Descrição</label>
                                <input type="text" name="descricao" value='`+eventos.descricao+`' class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control value='`+eventos.status+`'">
                                    <option value=""></option>
                                    <option value="Concluido">Concluído</option>
                                    <option value="Andamento">Em Andamento</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Data de Início</label>
                                <input type="date" value="`+dataInicioFormatada+`" name="data_inicio" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Data de Encerramento</label>
                                <input type="date" name="data_prazo" value="`+dataPrazoFormatada+`" class="form-control" required>
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
                success: function (response) {
                    // Manipule a resposta da API, se necessário
                    console.log(response);
                    // Feche o modal
                    $('#editEmployeeModal').modal('hide');
                },
                error: function (error) {
                    console.error('Erro na solicitação AJAX:', error);
                }
            });
        });
        },
        error: function (error) {
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
                success: function (response) {
                    // Manipule a resposta da API, se necessário
                    console.log(response);
                    // Feche o modal
                    $('#addEmployeeModal').modal('hide');
                },
                error: function (error) {
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
                var url = '/excluir-evento/' + title; // Substitua pelo URL correto da sua rota de exclusão de evento

                $.ajax({
                    type: 'DELETE', // Use o método DELETE para solicitar exclusão
                    url: url,
                    success: function (response) {
                        // Manipule a resposta da API, se necessário
                        console.log(response);
                        // Recarregue a página ou atualize a lista de eventos, se necessário
                    },
                    error: function (error) {
                        console.error('Erro na solicitação AJAX:', error);
                    }
                });
            }
        });
</script>


<style>  body {
    color: #566787;
    background: #f5f5f5;
    font-family: 'Varela Round', sans-serif;
    font-size: 13px;
}
.table-wrapper {
    background: #fff;
    padding: 20px 25px;
    margin: 30px 0;
    border-radius: 3px;
    box-shadow: 0 1px 1px rgba(0,0,0,.05);
}
.table-title {
    padding-bottom: 15px;
    background: #435d7d;
    color: #fff;
    padding: 16px 30px;
    margin: -20px -25px 10px;
    border-radius: 3px 3px 0 0;
}
.table-title h2 {
    margin: 5px 0 0;
    font-size: 24px;
}
.table-title .btn-group {
    float: right;
}
.table-title .btn {
    color: #fff;
    float: right;
    font-size: 13px;
    border: none;
    min-width: 50px;
    border-radius: 2px;
    border: none;
    outline: none !important;
    margin-left: 10px;
}
.table-title .btn i {
    float: left;
    font-size: 21px;
    margin-right: 5px;
}
.table-title .btn span {
    float: left;
    margin-top: 2px;
}
table.table tr th, table.table tr td {
    border-color: #e9e9e9;
    padding: 12px 15px;
    vertical-align: middle;
}
table.table tr th:first-child {
    width: 60px;
}
table.table tr th:last-child {
    width: 100px;
}
table.table-striped tbody tr:nth-of-type(odd) {
    background-color: #fcfcfc;
}
table.table-striped.table-hover tbody tr:hover {
    background: #f5f5f5;
}
table.table th i {
    font-size: 13px;
    margin: 0 5px;
    cursor: pointer;
}
table.table td:last-child i {
    opacity: 0.9;
    font-size: 22px;
    margin: 0 5px;
}
table.table td a {
    font-weight: bold;
    color: #566787;
    display: inline-block;
    text-decoration: none;
    outline: none !important;
}
table.table td a:hover {
    color: #2196F3;
}
table.table td a.edit {
    color: #FFC107;
}
table.table td a.delete {
    color: #F44336;
}
table.table td i {
    font-size: 19px;
}
table.table .avatar {
    border-radius: 50%;
    vertical-align: middle;
    margin-right: 10px;
}
.pagination {
    float: right;
    margin: 0 0 5px;
}
.pagination li a {
    border: none;
    font-size: 13px;
    min-width: 30px;
    min-height: 30px;
    color: #999;
    margin: 0 2px;
    line-height: 30px;
    border-radius: 2px !important;
    text-align: center;
    padding: 0 6px;
}
.pagination li a:hover {
    color: #666;
}
.pagination li.active a, .pagination li.active a.page-link {
    background: #03A9F4;
}
.pagination li.active a:hover {
    background: #0397d6;
}
.pagination li.disabled i {
    color: #ccc;
}
.pagination li i {
    font-size: 16px;
    padding-top: 6px
}
.hint-text {
    float: left;
    margin-top: 10px;
    font-size: 13px;
}
/* Custom checkbox */
.custom-checkbox {
    position: relative;
}
.custom-checkbox input[type="checkbox"] {
    opacity: 0;
    position: absolute;
    margin: 5px 0 0 3px;
    z-index: 9;
}
.custom-checkbox label:before{
    width: 18px;
    height: 18px;
}
.custom-checkbox label:before {
    content: '';
    margin-right: 10px;
    display: inline-block;
    vertical-align: text-top;
    background: white;
    border: 1px solid #bbb;
    border-radius: 2px;
    box-sizing: border-box;
    z-index: 2;
}
.custom-checkbox input[type="checkbox"]:checked + label:after {
    content: '';
    position: absolute;
    left: 6px;
    top: 3px;
    width: 6px;
    height: 11px;
    border: solid #000;
    border-width: 0 3px 3px 0;
    transform: inherit;
    z-index: 3;
    transform: rotateZ(45deg);
}
.custom-checkbox input[type="checkbox"]:checked + label:before {
    border-color: #03A9F4;
    background: #03A9F4;
}
.custom-checkbox input[type="checkbox"]:checked + label:after {
    border-color: #fff;
}
.custom-checkbox input[type="checkbox"]:disabled + label:before {
    color: #b8b8b8;
    cursor: auto;
    box-shadow: none;
    background: #ddd;
}
/* Modal styles */
.modal .modal-dialog {
    max-width: 400px;
}
.modal .modal-header, .modal .modal-body, .modal .modal-footer {
    padding: 20px 30px;
}
.modal .modal-content {
    border-radius: 3px;
}
.modal .modal-footer {
    background: #ecf0f1;
    border-radius: 0 0 3px 3px;
}
.modal .modal-title {
    display: inline-block;
}
.modal .form-control {
    border-radius: 2px;
    box-shadow: none;
    border-color: #dddddd;
}
.modal textarea.form-control {
    resize: vertical;
}
.modal .btn {
    border-radius: 2px;
    min-width: 100px;
}
.modal form label {
    font-weight: normal;
}</style>
