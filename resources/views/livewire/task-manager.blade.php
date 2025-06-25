<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <section class="p-6 lg:p-8 bg-white border-b border-gray-200">
                <x-application-logo class="block h-12 w-auto" />
                <h1 class="mt-8 text-2xl font-medium text-gray-900">Bem-vindo à sua aplicação Jetstream!</h1>
                <p class="mt-6 text-gray-500 leading-relaxed">
                    Laravel Jetstream fornece um ponto de partida robusto e elegante para sua próxima aplicação Laravel.
                </p>
            </section>

            <main class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8 p-6 lg:p-8">
                {{-- Título e descrição --}}
                <div class="md:col-span-2">
                    <h2 class="text-lg font-medium text-gray-900">Gerenciar Tarefas</h2>
                    <p class="mt-1 text-sm text-gray-600">Crie, edite e gerencie suas tarefas. Use os filtros e navegue pela lista paginada.</p>
                </div>

                {{-- Formulário --}}
                <div class="md:col-span-2 bg-white shadow sm:rounded-md">
                    <form wire:submit.prevent="{{ $submit ?? 'save' }}">
                        <div class="px-4 py-5 sm:p-6 space-y-6">
                            {{-- Campos de entrada --}}
                            @php
                            $campos = [
                                ['id' => 'title', 'label' => 'Título', 'type' => 'input'],
                                ['id' => 'description', 'label' => 'Descrição', 'type' => 'textarea'],
                                ['id' => 'priority', 'label' => 'Prioridade', 'type' => 'select'],
                                ['id' => 'status', 'label' => 'Status', 'type' => 'select'],
                            ];
                            @endphp

                            @foreach ($campos as $campo)
                                <div>
                                    <label for="{{ $campo['id'] }}" class="block text-sm font-medium text-gray-700">
                                        {{ $campo['label'] }}
                                    </label>

                                    @if ($campo['type'] === 'textarea')
                                        <textarea id="{{ $campo['id'] }}" wire:model.defer="{{ $campo['id'] }}" rows="3"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                    @elseif ($campo['type'] === 'select')
                                        <select id="{{ $campo['id'] }}" wire:model.defer="{{ $campo['id'] }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            @if($campo['id'] === 'priority')
                                                <option value="low">Baixa</option>
                                                <option value="medium">Média</option>
                                                <option value="high">Alta</option>
                                                <option value="urgent">Urgente</option>
                                            @else
                                                <option value="pending">Pendente</option>
                                                <option value="in_progress">Em progresso</option>
                                                <option value="completed">Concluída</option>
                                                <option value="skipped">Pulada</option>
                                            @endif
                                        </select>
                                    @else
                                        <input type="text" id="{{ $campo['id'] }}" wire:model.defer="{{ $campo['id'] }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                    @endif

                                    @error($campo['id']) <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                                </div>
                            @endforeach

                            {{-- Datas --}}
                            @foreach ([['start_time', 'Início'], ['due_time', 'Prazo']] as [$id, $label])
                                <div>
                                    <label for="{{ $id }}" class="block text-sm font-medium text-gray-700">{{ $label }}</label>
                                    <input type="datetime-local" id="{{ $id }}" wire:model.defer="{{ $id }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                    @error($id) <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                                </div>
                            @endforeach

                            {{-- Recorrência --}}
                            <div class="flex items-center space-x-2">
                                <input type="checkbox" id="is_recurring" wire:model.live="is_recurring"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                                <label for="is_recurring" class="text-sm font-medium text-gray-700">Tarefa recorrente</label>
                            </div>
                            @if($is_recurring)
                                <div>
                                    <label for="recurrence_type" class="block text-sm font-medium text-gray-700">Tipo de recorrência</label>
                                    <select id="recurrence_type" wire:model="recurrence_type"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Selecione</option>
                                        <option value="daily">Diária</option>
                                        <option value="weekly">Semanal</option>
                                        <option value="monthly">Mensal</option>
                                    </select>
                                    @error('recurrence_type') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                                </div>
                            @endif
                        </div>

                        <div class="flex justify-end px-4 py-3 bg-gray-50 text-right sm:px-6 rounded-b-md shadow">
                            <button type="submit"
                                class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-white font-semibold shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                {{ $taskId ? 'Atualizar' : 'Criar' }}
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Tabela --}}
                <div class="md:col-span-2 overflow-x-auto bg-white rounded-lg shadow mt-6">
                    <table class="min-w-full divide-y divide-gray-200 w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                @foreach (['Título', 'Prioridade', 'Status', 'Início', 'Prazo', 'Recorrente', 'Ações'] as $col)
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $col }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($tasks as $task)
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $task->title }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ ucfirst($task->priority) }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ str_replace('_', ' ', ucfirst($task->status)) }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $task->start_time }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $task->due_time }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $task->is_recurring ? 'Sim' : 'Não' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500 text-right space-x-2">
                                        <button wire:click="edit({{ $task->id }})" class="text-indigo-600 hover:text-indigo-900 font-semibold">Editar</button>
                                        <button wire:click="delete({{ $task->id }})" class="text-red-600 hover:text-red-900 font-semibold">Excluir</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-gray-500">Nenhuma tarefa encontrada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4 px-4">
                        {{ $tasks->links() }}
                    </div>
                </div>
            </main>
        </div>  
    </div>
</div>
