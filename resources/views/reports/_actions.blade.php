{{-- Shared action bar + email modal for all four reports.
     Expects: $type (report type string), $params (array of hidden inputs to propagate), $recipients --}}

<div class="flex flex-wrap items-center justify-end gap-2">
    @if(session('success'))
        <div class="text-sm text-green-700 bg-green-50 border border-green-200 px-3 py-1.5 rounded-md mr-auto">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="text-sm text-red-700 bg-red-50 border border-red-200 px-3 py-1.5 rounded-md mr-auto">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('reports.save') }}" class="inline">
        @csrf
        <input type="hidden" name="type" value="{{ $type }}">
        @foreach($params as $k => $v)
            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
        @endforeach
        <button type="submit" class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
            Save Snapshot
        </button>
    </form>

    <a href="{{ route('reports.pdf', ['type' => $type] + $params) }}"
        class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
        Print / Download PDF
    </a>

    <button type="button" x-data @click="document.getElementById('email-modal').classList.remove('hidden')"
        class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700">
        Email Report
    </button>
</div>

@if($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mt-4 rounded-md">
        <ul class="list-disc pl-5 text-sm text-red-700">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div id="email-modal" class="fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-6 border w-full max-w-lg shadow-lg rounded-md bg-white">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Email Report</h3>
        <form method="POST" action="{{ route('reports.email') }}">
            @csrf
            <input type="hidden" name="type" value="{{ $type }}">
            @foreach($params as $k => $v)
                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
            @endforeach

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Recipient (Manager or Admin)</label>
                <select name="recipient_email" required
                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full">
                    <option value="">Select recipient...</option>
                    @foreach($recipients as $r)
                        <option value="{{ $r->email }}">{{ $r->first_name }} {{ $r->last_name }} — {{ $r->email }} ({{ $r->role }})</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                <input type="text" name="subject" required value="{{ old('subject', $emailSubject ?? '') }}"
                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                <textarea name="body" rows="4" required
                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full">{{ old('body', 'Please find the attached report.') }}</textarea>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('email-modal').classList.add('hidden')"
                    class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50">Cancel</button>
                <button type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">Send</button>
            </div>
        </form>
    </div>
</div>
