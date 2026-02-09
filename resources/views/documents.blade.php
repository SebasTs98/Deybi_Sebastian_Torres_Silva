<!DOCTYPE html>
<html>

<head>

<!-- ================= TITULO ================= -->
<title>Panel Documentos</title>

<!-- ================= BOOTSTRAP CSS ================= -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- ================= BOOTSTRAP JS ================= -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</head>

<body class="bg-light">

<div class="container mt-5">

<h2 class="mb-4">📄 Sistema Radicación Documentos</h2>

<!-- ================= LOADER ================= -->
<div id="loader" class="alert alert-info d-none">

<div class="spinner-border spinner-border-sm"></div>
Procesando documento...

</div>

<!-- ================= FORMULARIO ================= -->

<div class="card shadow mb-4">

<div class="card-header bg-primary text-white">
Radicar Documento
</div>

<div class="card-body">

<form id="uploadForm">

<div class="row">

<div class="col-md-4 mb-3">
<label class="form-label">Número de presentación</label>
<input class="form-control" name="filing_number" required>
</div>

<div class="col-md-4 mb-3">
<label class="form-label">Tipo Documento</label>
<select class="form-control" name="document_type">
<option value="contractor_invoice">Contractor</option>
<option value="supplier_invoice">Supplier</option>
<option value="general_invoice">General</option>
</select>
</div>

<div class="col-md-4 mb-3">
<label class="form-label">Email</label>
<input class="form-control" name="email_recipient" type="email" required>
</div>

<div class="col-md-12 mb-3">
<label class="form-label">Archivo</label>
<input class="form-control" type="file" name="file" required>
</div>

</div>

<button id="submitBtn" class="btn btn-success">
Enviar Documento
</button>

</form>

</div>

</div>

<!-- ================= TABLA DOCUMENTOS ================= -->

<div class="card shadow">

<div class="card-header bg-dark text-white">
Listado Documentos
</div>

<div class="card-body">

<table class="table table-striped table-hover">

<thead class="table-dark">

<tr>
<th>ID</th>
<th>Filing</th>
<th>Tipo</th>
<th>Status</th>
<th>Email</th>
<th>Archivo</th>
<th>Fecha</th>
</tr>

</thead>

<tbody id="docs"></tbody>

</table>

</div>

</div>

</div>

<!-- ================= MODAL ERROR ================= -->

<div class="modal fade" id="errorModal" tabindex="-1">

<div class="modal-dialog">

<div class="modal-content">

<div class="modal-header bg-danger text-white">

<h5 class="modal-title">Error en procesamiento</h5>

<button type="button" class="btn-close" data-bs-dismiss="modal"></button>

</div>

<div class="modal-body" id="errorContent">

</div>

</div>

</div>

</div>

<script>

/**
 * ================= CARGAR DOCUMENTOS =================
 * Consulta API y llena tabla
 */
async function loadDocs() {

const res = await fetch('/api/documents');
const json = await res.json();

let rows = '';

json.data.forEach(doc => {

rows += `
<tr>
<td>${doc.id}</td>
<td>${doc.filing_number}</td>
<td>${doc.document_type}</td>

<td>
<span class="badge ${
doc.status === 'validated' ? 'bg-success' :
doc.status === 'rejected' ? 'bg-danger' :
doc.status === 'processing' ? 'bg-info' :
'bg-warning'
}">
${doc.status}
</span>
</td>

<td>${doc.email_recipient ?? ''}</td>
<td>${doc.original_filename ?? ''}</td>
<td>${doc.created_at ?? ''}</td>

</tr>
`;

});

document.getElementById('docs').innerHTML = rows;

}

/**
 * ================= SUBMIT FORM =================
 * Maneja envío y errores profesionales
 */
document.getElementById('uploadForm').addEventListener('submit', async e => {

e.preventDefault();

const loader = document.getElementById('loader');
const button = document.getElementById('submitBtn');

loader.classList.remove('d-none');

button.disabled = true;
button.innerText = "Procesando...";

const formData = new FormData(e.target);

try {

const res = await fetch('/api/documents', {
method: 'POST',
body: formData
});

// 🔥 Leer respuesta como texto primero
const text = await res.text();

let json;

try {
json = JSON.parse(text);
} catch {
json = { message: text };
}

// SI ERROR (422 o 500)
if (!res.ok || json.status === 'error') {

document.getElementById('errorContent').innerHTML =
`<pre style="white-space:pre-wrap;">${json.message ?? 'Error desconocido'}</pre>`;

let modal = new bootstrap.Modal(document.getElementById('errorModal'));
modal.show();

} else {

e.target.reset();
loadDocs();

}

} catch (error) {

document.getElementById('errorContent').innerHTML = error.message;

let modal = new bootstrap.Modal(document.getElementById('errorModal'));
modal.show();

}

loader.classList.add('d-none');

button.disabled = false;
button.innerText = "Enviar Documento";

});

// Cargar documentos al iniciar
loadDocs();

</script>

</body>
</html>
