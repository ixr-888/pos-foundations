<form method="post" action="{{ route('tasks.destroy', $task) }}" data-confirm="Archive this task? It will disappear from the active list, and its database record will be kept.">
    @csrf
    @method('DELETE')
    <button type="submit" class="text-button danger-text" aria-label="Archive {{ $task->title }}">Archive</button>
</form>
