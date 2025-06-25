<?php

namespace App\Livewire;

use App\Events\TaskCreated;
use App\Mail\TasksCreated;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use App\Models\Task;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class TaskManager extends Component
{
    use WithPagination;

    public $taskId;
    public $title;
    public $description;
    public $priority = 'medium';
    public $status = 'pending';
    public $start_time;
    public $due_time;
    public $is_recurring = false;
    public $recurrence_type = '';

    public $search = '';

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'priority' => 'required|in:low,medium,high,urgent',
        'status' => 'required|in:pending,in_progress,completed,skipped',
        'start_time' => 'nullable|date',
        'due_time' => 'nullable|date|after_or_equal:start_time',
        'is_recurring' => 'boolean',
        'recurrence_type' => 'nullable|string',
    ];

    public function render()
    {
        $tasks = Task::where('user_id', auth()->id())
            ->where('title', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(5);

        return view('livewire.task-manager', compact('tasks'))
            ->layout('layouts.app');
    }

    public function resetFields()
    {
        $this->taskId = null;
        $this->title = '';
        $this->description = '';
        $this->priority = 'medium';
        $this->status = 'pending';
        $this->start_time = null;
        $this->due_time = null;
        $this->is_recurring = false;
        $this->recurrence_type = null;
    }

    public function save()
    {
        $this->validate();

        Task::updateOrCreate(
            ['id' => $this->taskId],
            [
                'user_id' => auth()->id(),
                'title' => $this->title,
                'description' => $this->description,
                'priority' => $this->priority,
                'status' => $this->status,
                'start_time' => $this->start_time ? Carbon::parse($this->start_time) : null,
                'due_time' => $this->due_time ? Carbon::parse($this->due_time) : null,
                'is_recurring' => $this->is_recurring,
                'recurrence_type' => $this->recurrence_type,
            ]
        );
        TaskCreated::dispatch($this->title, Auth::user()->email);
        session()->flash('message', $this->taskId ? 'Tarefa atualizada!' : 'Tarefa criada!');
        $this->resetFields();
    }

    public function edit($id)
    {
        $task = Task::findOrFail($id);

        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        $this->taskId = $task->id;
        $this->title = $task->title;
        $this->description = $task->description;
        $this->priority = $task->priority;
        $this->status = $task->status;
        $this->start_time = optional($task->start_time)->format('Y-m-d\TH:i');
        $this->due_time = optional($task->due_time)->format('Y-m-d\TH:i');
        $this->is_recurring = $task->is_recurring;
        $this->recurrence_type = $task->recurrence_type;
    }

    public function delete($id)
    {
        $task = Task::findOrFail($id);
        if ($task->user_id === auth()->id()) {
            $task->delete();
            session()->flash('message', 'Tarefa excluída!');
        }
    }
}
