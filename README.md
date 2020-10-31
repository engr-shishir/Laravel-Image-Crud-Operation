># Laravel Image Crud Operation
<br><br><br>




+ `php artisan make:controller StudentController`
+ `php artisan make:model Student -m`
+ `php artisan serve`
+ `php artisan migrate`
<br><br><br>



>## web.php
```php
Route::get('/addStudent', [StudentController::class, 'addStudent']);
Route::post('/addStudent', [StudentController::class, 'storeStudent'])->name('student.store');
Route::get('/allStudent', [StudentController::class, 'allStudent']);
Route::get('/editStudent/{id}', [StudentController::class, 'editStudent']);
Route::post('/update', [StudentController::class, 'update'])->name('student.update');
Route::get('/delete/{id}', [StudentController::class, 'delete'])->name('student.delete');
```
<br><br><br>





>## StudentController.php
```php
class StudentController extends Controller
{
    public function addStudent()
    {
        return view('addStudent');
    }

    public function storeStudent(Request $request)
    {
        $name = $request->name;
        $image = $request->file('file');
        $extension =$image->extension();
        if( $extension == 'jpeg')
        {
            $imageName = $name. '.' .$image->extension();
            $image->move(public_path('images'), $imageName);
            $student = new Student();
            $student->name = $name;
            $student->profileimage = $imageName;
            $student->save();
    
            return back()->with('student_added', 'Student record has been Inserted');
        }else{
            return back()->with('student_added', 'Image type should be jpg');  
        }






    }


    public function allStudent()
    {
        $students = Student::all();
        return view('allStudent', compact('students'));
    }

    public function editStudent($id)
    {
        $student = Student::find($id);
        return view('editStudent', compact('student'));
    }


    public function update(Request $request)
    {
        
        $name = $request->name;
        $image = $request->file('file');
        $extension =$image->extension();
        if( $extension == 'jpeg'){

            $old_image_path = public_path("images/{$request->name}.$extension");
            if(File::exists($old_image_path)) {
                File::delete($old_image_path);
            }

            $imageName = $name. '.' .$extension;
            $image->move(public_path('images'), $imageName);
            $student = Student::find($request->id);
            $student->name = $name;
            $student->profileimage = $imageName;
            $student->save();
            return back()->with('student_updated', 'stuent Updated Successfuly');
        }else{
            return back()->with('student_updated', 'Image type should be jpg');
        }
    }

    public function delete($id)
    {
        $student = Student::find($id);
        unlink(public_path('images').'/'.$student->profileimage);
 
        $student->delete();

        return back()->with('student_deleted', 'Student Deleted Successfuly');
    }
}
```
<br><br><br>










>## addStudent.blade.php
```php
<section class="py-3">
 <div class="container">
  <div class="row">
   <div class="col-md-6 m-auto">
    <div class="card">
     <div class="card-header text-center">
      Add New Student
      <a class="btn btn-primary" href="/allStudent">All Student</a>
     </div>
     <div class="card-body">
      @if(Session::has('student_added'))
      <div class="alert alert-primary" role="alert">
       {{Session::get('student_added')}}
     </div>
     @endif
     <form method="POST" action="{{route('student.store')}}" enctype="multipart/form-data">
       @csrf
       <div class="form-group">
         <label>Name</label>
         <input type="text" name="name" class="form-control" />
       </div>
       <div class="form-group">
         <label>Chose Profile Image</label>
         <input type="file" name="file" class="form-control" onchange="previewfile(this)" />
         <img id="previewimg"style="max-width: 130px; margin-top:20px;" />
       </div>

       <button type="submit" class="btn btn-info form-control">Submit</button>
     </form>
     </div>
    </div>
   </div>
  </div>
 </div>
</section>

```
<br><br><br>






>## allStudent.blade.php
```php
<section class="py-3">
 <div class="container">
  <div class="row">
   <div class="col-md-10 m-auto">
    <div class="card">
     <div class="card-header text-center">
      All students
      <a class="btn btn-primary" href="/addStudent">Add Student</a>
     </div>
     <div class="card-body">
      
      @if(Session::has('student_deleted'))
      <div class="alert alert-success" role="alert">
       {{Session::get('student_deleted')}}
     </div>
     @endif
      <table class="table table-dark">
       <thead>
         <tr class="text-center">
           <th scope="col">Name</th>
           <th scope="col">Image</th>
           <th scope="col">Action</th>
         </tr>
       </thead>
       <tbody>
        
        @foreach ($students as $student)
         <tr class="text-center" >
           <td>{{$student->name}}</td>
           <td>
            <img src="{{asset('images')}}/{{$student->profileimage}}" style="max-width: 100px;"/>
           </td>

           <td>
            <a  class="btn btn-warning btn-sm" href="/editStudent/{{$student->id}}">Edit</a>
           </td>

           <td>
            <a  class="btn btn-danger btn-sm" href="/delete/{{$student->id}}">Delete</a>
           </td>
         </tr>  
        @endforeach
       </tbody>
     </table>
     </div>
    </div>
   </div>
  </div>
 </div>
</section>
```
<br><br><br>






>## editStudent.blade.php
```php
<section class="py-3">
 <div class="container">
  <div class="row">
   <div class="col-md-6 m-auto">
    <div class="card">
     <div class="card-header text-center">
      Edit Student
      <a class="btn btn-primary" href="/allStudent">Back</a>
     </div>
     <div class="card-body">
      @if(Session::has('student_updated'))
      <div class="alert alert-success" role="alert">
       {{Session::get('student_updated')}}
     </div>
     @endif
     <form method="POST" action="{{route('student.update')}}" enctype="multipart/form-data">
       @csrf
     <input type="hidden" name="id" value="{{$student->id}}" />

       <div class="form-group">
         <label>Name</label>
         <input type="text" name="name" class="form-control" value="{{$student->name}}" />
       </div>
       <div class="form-group">
         <label>Chose Profile Image</label>
         <input type="file" name="file" class="form-control" onchange="previewfile(this)" />
         <img id="previewimg" alt="Profile Image" style="max-width: 130px; margin-top:20px;" src="{{asset('images')}}/{{$student->profileimage}}"/>
       </div>

       <button type="submit" class="btn btn-info form-control">update</button>
     </form>
     </div>
    </div>
   </div>
  </div>
 </div>
</section>
```
<br><br><br>








>## Using `Alert Toastr` use toastr Cdn ....... and include this condition
  ```php
  @if(Session::has('student_added'))
    <script>
      toastr.success("{!! Session::get('student_added') !!}");
    </script>
  @endif
  ```