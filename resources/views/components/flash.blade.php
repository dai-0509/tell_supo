@props(['success' => session('success'), 'error' => session('error'), 'status' => session('status')])

@if ($success)
  <div class="mb-4 rounded bg-green-100 px-4 py-3 text-green-800">{{ $success }}</div>
@endif
@if ($error)
  <div class="mb-4 rounded bg-red-100 px-4 py-3 text-red-800">{{ $error }}</div>
@endif
@if ($status)
  <div class="mb-4 rounded bg-blue-100 px-4 py-3 text-blue-800">{{ $status }}</div>
@endif

@if ($errors->any())
  <div class="mb-4 rounded bg-red-100 px-4 py-3 text-red-800">
    <ul class="list-disc pl-5">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif
