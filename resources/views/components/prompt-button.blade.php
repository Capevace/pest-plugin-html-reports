@props([
	'task',
	'label',
])


<button
	x-data="{ 
		isCopied: false,
		timeout: null,
		copyToClipboard(task) {
			this.isCopied = true;
			this.task = task;

			navigator.clipboard.writeText(this.prompt);
			
			clearTimeout(this.timeout);

			this.timeout = setTimeout(() => {
				this.isCopied = false;
			}, 1000);
		},
	}"
	type="button"
	class="relative inline-flex font-bold items-center justify-end gap-1 px-2 py-1 text-sm font-medium text-gray-300 bg-zinc-900 border border-zinc-700 hover:bg-zinc-800"
	x-on:click="copyToClipboard('{{ $task }}')"
	x-show="window.navigator.clipboard"
>
	<span class="text-zinc-500 absolute left-0 px-2 text-xs">COPY</span>
	<span class="text-right text-green-500" x-show="isCopied" x-cloak>Copied</span>
	<span class="text-right" x-show="!isCopied">{{ $label }}</span>
</button>