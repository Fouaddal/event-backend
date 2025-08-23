<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $event->title }} - Invitation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 40px; background: #f7f9fc; }
        .card { max-width: 600px; margin: auto; padding: 30px; border-radius: 12px; background: #fff; box-shadow: 0 8px 20px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
<div class="card text-center">
    <h2>{{ $event->title }}</h2>
    <p><strong>Host:</strong> {{ $event->user->name }}</p>
    <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($event->date)->format('F j, Y') }}</p>
    <p><strong>Time:</strong> {{ \Carbon\Carbon::parse($event->time)->format('h:i A') }}</p>
    @if($event->price)
        <p><strong>Price:</strong> ${{ $event->price }}</p>
    @endif

    <form action="{{ url('/invite/'.$event->invitation_code.'/respond') }}" method="POST" class="mt-4">
        @csrf
        <input type="text" name="name" placeholder="Your Name" class="form-control mb-3" required>
        <button type="submit" name="response" value="coming" class="btn btn-success">I will come</button>
        <button type="submit" name="response" value="not_coming" class="btn btn-danger">I cannot come</button>
    </form>

    @if(session('success'))
        <div class="alert alert-success mt-3">{{ session('success') }}</div>
    @endif
</div>
</body>
</html>
