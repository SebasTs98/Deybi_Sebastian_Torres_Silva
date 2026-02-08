<!DOCTYPE html>
<html>

<head>

<title>Panel Radicación</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="container mt-5">

<h2>Radicación de Documentos</h2>

<div id="mensaje"></div>

<form id="formulario">

<input class="form-control mb-2" name="filing_number" placeholder="Filing Number">

<input class="form-control mb-2" name="document_type" placeholder="Tipo Documento">

<input class="form-control mb-2" name="email_recipient" placeholder="Email">

<button class="btn btn-primary">Guardar</button>

</form>

<hr>

<table class="table">

<thead>

<tr>
<th>ID</th>
<th>Filing</th>
<th>Tipo</th>
<th>Email</th>
</tr>

</thead>

<tbody id="tabla">

@foreach($documents as $doc)

<tr>
<td>{{ $doc->id }}</td>
<td>{{ $doc->filing_number }}</td>
<td>{{ $doc->document_type }}</td>
<td>{{ $doc->email_recipient }}</td>
</tr>

@endforeach

</tbody>

</table>

<script>

document.getElementById("formulario").addEventListener("submit", async function(e){

e.preventDefault();

let formData = new FormData(this);

let response = await fetch('/api/documents',{

method:'POST',
headers:{
'Accept':'application/json'
},
body: formData

});

let data = await response.json();

if(response.ok){

document.getElementById("mensaje").innerHTML =
'<div class="alert alert-success">Documento guardado</div>';

location.reload();

}else{

document.getElementById("mensaje").innerHTML =
'<div class="alert alert-danger">Error validación</div>';

}

});

</script>

</body>

</html>
