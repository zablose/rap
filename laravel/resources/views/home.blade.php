<h1>Home</h1>

@role(['user'])
<h1>User</h1>
@endrole

@role(['admin'])
<h1>Admin</h1>
@endrole

@permission(['password.update'])
<h1>Password Update</h1>
@endpermission
