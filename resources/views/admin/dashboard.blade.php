<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Парсер и Админка</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen p-6">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">📊 Панель парсинга</h1>
            <form action="{{ route('parser.run') }}" method="POST">
                @csrf
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    Запустить парсинг
                </button>
            </form>
        </div>

        @if(session('status'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded whitespace-pre-line">{{ session('status') }}</div>
        @endif

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">URL</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Сниппет</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Дата</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($items as $item)
                        <tr>
                            <td class="px-4 py-3 text-sm text-blue-600">{{ $item->url }}</td>
                            <td class="px-4 py-3 text-sm">{{ $item->status_code }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600 max-w-xs truncate">{{ $item->content_snippet }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $item->parsed_at->format('d.m.Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500">Нет данных. Нажмите "Запустить парсинг"</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4">{{ $items->links() }}</div>
        </div>
    </div>
</body>
</html>