<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use File;


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
