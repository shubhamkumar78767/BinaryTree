<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <title>Document</title>
    <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
</head>

<body>
    <div class="container">
        <div class="row mt-5 bg-light">
            <div class="col-md-6 offset-3 mt-3">
                <form action="{{ url('/create') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Root</label>
                        <select name="root" id="root" class="form-select">
                            <option value="">Select</option>
                            @foreach($allRootElement as $rootelement)
                            <option value="{{ $rootelement->id }}">{{ $rootelement->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Position</label>
                        <select name="position" id="position" class="form-select">
                            <option value="1">Left</option>
                            <option value="2">Right</option>
                        </select>
                    </div>
                    <div class="mb-3 col-md-4 offset-4">
                        <button type="submit" class="form-control btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>