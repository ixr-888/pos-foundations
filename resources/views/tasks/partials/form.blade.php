@php
    $titleValue = old('title', $task->title);
    $descriptionValue = old('description', $task->description);
    $dateValue = old('due_date', $task->due_date?->format('Y-m-d'));
@endphp
<div class="field">
    <label for="title">Task title <span class="required-mark">*</span></label>
    <input id="title" name="title" type="text" value="{{ is_string($titleValue) ? $titleValue : '' }}" required maxlength="255" autofocus @error('title') aria-invalid="true" aria-describedby="title-error" @enderror>
    @error('title')<p class="field-error" id="title-error">{{ $message }}</p>@enderror
</div>
<div class="field">
    <label for="description">Description <span class="optional-label">Optional</span></label>
    <textarea id="description" name="description" rows="5" maxlength="5000" aria-describedby="description-help @error('description') description-error @enderror" @error('description') aria-invalid="true" @enderror>{{ is_string($descriptionValue) ? $descriptionValue : '' }}</textarea>
    <p class="field-help" id="description-help">Add the details someone needs to finish this task. Up to 5,000 characters.</p>
    @error('description')<p class="field-error" id="description-error">{{ $message }}</p>@enderror
</div>
<div class="form-grid">
    <div class="field"><label for="due_date">Due date <span class="required-mark">*</span></label><input id="due_date" name="due_date" type="date" value="{{ is_string($dateValue) ? $dateValue : '' }}" required @error('due_date') aria-invalid="true" aria-describedby="date-error" @enderror>@error('due_date')<p class="field-error" id="date-error">{{ $message }}</p>@enderror</div>
    <div class="field"><label for="priority">Priority <span class="required-mark">*</span></label><select id="priority" name="priority" required>@foreach (\App\Models\Task::PRIORITIES as $value => $label)<option value="{{ $value }}" @selected(old('priority', $task->priority) === $value)>{{ $label }}</option>@endforeach</select>@error('priority')<p class="field-error">{{ $message }}</p>@enderror</div>
</div>
<div class="field">
    <label for="status">Status <span class="required-mark">*</span></label>
    <select id="status" name="status" required aria-describedby="status-help">@foreach (\App\Models\Task::STATUSES as $value => $label)<option value="{{ $value }}" @selected(old('status', $task->status) === $value)>{{ $label }}</option>@endforeach</select>
    <p id="status-help" class="field-help">{{ $task->exists ? 'Changing an unfinished task to Done generates a completion email. Saving a task that is already Done does not send another.' : 'Completion emails are generated when an existing task changes to Done.' }}</p>
    @error('status')<p class="field-error">{{ $message }}</p>@enderror
</div>
<div class="form-actions"><button type="submit" class="button button-primary">{{ $task->exists ? 'Save changes' : 'Create task' }}</button><a class="button button-outline" href="{{ $task->exists ? route('tasks.show', $task) : route('tasks.index') }}">Cancel</a></div>
