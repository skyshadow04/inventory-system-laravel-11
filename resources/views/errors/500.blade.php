<x-layout>
    <div class="py-10 px-3 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto text-center">
            <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-red-50 text-red-700 mb-6">
                <span class="text-3xl font-bold">500</span>
            </div>
            <h1 class="text-3xl font-bold text-slate-900 mb-3">Server error</h1>
            <p class="text-slate-600 mb-6">Something went wrong on our end. Please try again in a few minutes.</p>
            <div class="flex flex-col sm:flex-row justify-center gap-3">
                <a href="{{ url('/') }}" class="inline-flex items-center justify-center px-5 py-3 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800">Back to home</a>
                <a href="{{ url()->previous() }}" class="inline-flex items-center justify-center px-5 py-3 rounded-xl bg-white border border-slate-200 text-slate-700 text-sm font-semibold hover:bg-slate-50">Go back</a>
            </div>
        </div>
    </div>
</x-layout>
