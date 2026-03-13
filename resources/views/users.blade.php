<!DOCTYPE html>
<html>
<head>

<title>Quản lý nhân viên</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="container mt-4">

<h2>Quản lý nhân viên</h2>


<h5>Backend API</h5>

<input id="apiLink" class="form-control mb-2" readonly>

<a id="openApi" target="_blank" class="btn btn-info mb-4">
Mở API Backend
</a>


<!-- tìm user -->
<div class="mb-3">

<input type="number" id="searchId" placeholder="Nhập ID nhân viên">

<button onclick="findUser()" class="btn btn-primary">
Tìm
</button>

<button onclick="loadUsers()" class="btn btn-secondary">
Xem tất cả
</button>

</div>


<h4>Thêm nhân viên</h4>

<input id="name" placeholder="Tên" class="form-control mb-2">

<input id="phone" placeholder="SĐT" class="form-control mb-2">

<input id="address" placeholder="Địa chỉ" class="form-control mb-2">

<input id="birthday" type="date" class="form-control mb-2">

<input id="gender" placeholder="Giới tính" class="form-control mb-2">

<button onclick="addUser()" class="btn btn-success mb-4">
Thêm nhân viên
</button>



<table class="table table-bordered">

<thead>

<tr>

<th>ID</th>
<th>Name</th>
<th>Phone</th>
<th>Address</th>
<th>Birthday</th>
<th>Gender</th>
<th>Action</th>

</tr>

</thead>

<tbody id="userTable"></tbody>

</table>



<script>

const API = "/api/users"


function loadUsers(){

document.getElementById("apiLink").value = API
document.getElementById("openApi").href = API

fetch(API)

.then(res=>res.json())

.then(data=>{

let rows=""

data.forEach(u=>{

rows+=`
<tr>

<td>${u.id}</td>

<td>${u.name}</td>

<td>${u.phone}</td>

<td>${u.address}</td>

<td>${u.birthday}</td>

<td>${u.gender}</td>

<td>

<button onclick="deleteUser(${u.id})" class="btn btn-danger btn-sm">
Xóa
</button>

<button onclick="editUser(${u.id})" class="btn btn-warning btn-sm">
Sửa
</button>

</td>

</tr>
`

})

document.getElementById("userTable").innerHTML=rows

})

}



function findUser(){

let id=document.getElementById("searchId").value

let url = API + "/" + id

document.getElementById("apiLink").value = url
document.getElementById("openApi").href = url

fetch(url)

.then(res=>res.json())

.then(u=>{

let row=`

<tr>

<td>${u.id}</td>

<td>${u.name}</td>

<td>${u.phone}</td>

<td>${u.address}</td>

<td>${u.birthday}</td>

<td>${u.gender}</td>

<td></td>

</tr>

`

document.getElementById("userTable").innerHTML=row

})

}



function addUser(){

fetch(API,{

method:"POST",

headers:{
"Content-Type":"application/json"
},

body:JSON.stringify({

name:document.getElementById("name").value,
phone:document.getElementById("phone").value,
address:document.getElementById("address").value,
birthday:document.getElementById("birthday").value,
gender:document.getElementById("gender").value

})

})

.then(()=>loadUsers())

}



function deleteUser(id){

fetch(API+"/"+id,{

method:"DELETE"

})

.then(()=>loadUsers())

}



function editUser(id){

let name=prompt("Tên mới")
let phone=prompt("SĐT mới")
let address=prompt("Địa chỉ mới")
let birthday=prompt("Ngày sinh YYYY-MM-DD")
let gender=prompt("Giới tính")

fetch(API+"/"+id,{

method:"PUT",

headers:{
"Content-Type":"application/json"
},

body:JSON.stringify({

name:name,
phone:phone,
address:address,
birthday:birthday,
gender:gender

})

})

.then(()=>loadUsers())

}


loadUsers()

</script>


</body>

</html>