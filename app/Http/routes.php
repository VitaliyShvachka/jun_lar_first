<?php
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

use \App\Models\Task;
use Illuminate\Http\Request;
Route::get('/', function () {
    return view('welcome');
});
Route::get('/about', function (){
  return view('about');
});

Route::group(['prefix' => 'task'], function () {
    Route::get('/', function () {
        $tasks = Task::all();
        return view('task.index', [
            'tasks' => $tasks,
        ]);
    })->name('task.index');

    Route::post('/', function (\Illuminate\Http\Request $request) {
        $validator = Validator::make(
            $request->all(),//request to array converting
            [
                'name' => 'required|max:200|unique:tasks,name',
            ]
        );
        if ($validator->fails()) {
            return redirect()
                ->route('task.index')
                ->withInput()
                ->withErrors($validator);
        }
        $task = new Task();
        $task->name = $request->name;
        $task->save();
        return redirect()->route('task.index');
    })->name('task.store');

    Route::get('/edit/{id}', function ($id){
      $task = Task::find($id);
      return view('task.edit', compact('task'));
    })->name('task.edit');
  Route::post('/edit/{id}', function ($id, Request $request){
    $validator = Validator::make(
      $request->all(),//request to array converting
      [
        'name' => 'required|max:200|unique:tasks,name',
      ]
    );
    if ($validator->fails()) {
      return redirect()
        ->route('task.edit', $id)
        ->withInput()
        ->withErrors($validator);
    }
    $task = $request->all();
    Task::find($id)->update($task);
    return redirect()->route('task.index')->with('message','Задача успішно оновлена!');
  })->name('task.update');

    Route::delete('/{task}', function (Task $task) {
        $task->delete();
        return redirect()->route('task.index');
    })->where('task', '[0-9]+')->name('task.destroy');
});



