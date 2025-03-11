<!-- resources/views/import.blade.php -->
<form action="{{ route('import.process') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="file" name="excel_file" accept=".xls,.xlsx">
    <button type="submit">Import</button>
</form>