<div 
	class="flex items-start gap-4" 
	:class="{'opacity-50': test.todo}"
>
	<div class="w-16 flex-shrink-0 text-right font-bold">
		<template x-if="!test.error && !test.failure && !test.skipped"><span class="text-emerald-500">[PASS]</span></template>
		<template x-if="test.error || test.failure"><span class="text-red-500">[FAIL]</span></template>
		<template x-if="test.skipped"><span class="text-amber-500">[SKIP]</span></template>
	</div>
	<div class="min-w-0 flex-1">
		<div class="flex items-start justify-between gap-3">
			<div class="min-w-0 flex-1">
				<div class="flex items-center justify-between gap-2 w-full">
					<div class="text-white flex-shrink-0" x-text="test.test"></div>
					<div class="flex-shrink-0">
						{{-- <button
							type="button"
							@click.prevent="generateIssue(test)"
							class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium text-gray-300 bg-zinc-900 border border-zinc-700 hover:bg-zinc-800"
						>
							<span>Create in Linear</span>
						</button>

						 --}}
						<div 
							x-data="{ isOpen: false, openedWithKeyboard: false }" 
							class="relative w-fit" 
							x-on:keydown.esc.window="isOpen = false, openedWithKeyboard = false"
						>
							<button
								type="button"
								{{-- @click.prevent="navigator.clipboard.writeText(composePrompt(test))" --}}
								class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium text-gray-300 bg-zinc-900 border border-zinc-700 hover:bg-zinc-800"
								x-on:click="isOpen = ! isOpen" 
							>
								<span>Copy LLM Prompt</span>
							</button>

							<!-- Dropdown Menu -->
							<div 
								x-cloak 
								x-show="isOpen || openedWithKeyboard" 
								x-transition 
								x-trap="openedWithKeyboard" 
								x-on:click.outside="isOpen = false, openedWithKeyboard = false" 
								x-on:keydown.down.prevent="$focus.wrap().next()" 
								x-on:keydown.up.prevent="$focus.wrap().previous()" 
								class="absolute top-11 right-0 flex w-fit min-w-48 flex-col overflow-hidden rounded-sm border border-neutral-300 bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-900" 
								role="menu"
								x-data="{
									task: 'Analyze the Pest test case and respond accordingly.',

									get prompt() {
										return composePrompt(test, this.task);
									}
								}"
							>
								<div class="flex flex-col">
									<x-pest-reports::prompt-button
										task="Analyze the Pest test case and respond accordingly."
										label="Analyze and Respond"
									/>
									<x-pest-reports::prompt-button 
										task="Fix all issues of this test case until it passes successfully and reliably."
										label="Fix all issues"
									/>
									<x-pest-reports::prompt-button
										task="Make the test case more robust and reliable by adding more edge cases, assertions, and test cases."
										label="Add More Tests"
									/>
									<x-pest-reports::prompt-button
										task="Repair this broken test after a change in the codebase. Investigate what has changed using Git and adapt the tests to any _relevant_ changes of the codebase."
										label="Repair Implementation"
									/>
								</div>
								<div 
									class="flex-1 max-h-96 overflow-y-auto  p-5 text-[0.4rem] font-mono font-light text-zinc-400 whitespace-pre-wrap"
									x-text="prompt"
								></div>
								{{-- <a href="#" class="bg-neutral-50 px-4 py-2 text-sm text-neutral-600 hover:bg-neutral-900/5 hover:text-neutral-900 focus-visible:bg-neutral-900/10 focus-visible:text-neutral-900 focus-visible:outline-hidden dark:bg-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-50/5 dark:hover:text-white dark:focus-visible:bg-neutral-50/10 dark:focus-visible:text-white" role="menuitem">Dashboard</a>
								<a href="#" class="bg-neutral-50 px-4 py-2 text-sm text-neutral-600 hover:bg-neutral-900/5 hover:text-neutral-900 focus-visible:bg-neutral-900/10 focus-visible:text-neutral-900 focus-visible:outline-hidden dark:bg-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-50/5 dark:hover:text-white dark:focus-visible:bg-neutral-50/10 dark:focus-visible:text-white" role="menuitem">Subscription</a>
								<a href="#" class="bg-neutral-50 px-4 py-2 text-sm text-neutral-600 hover:bg-neutral-900/5 hover:text-neutral-900 focus-visible:bg-neutral-900/10 focus-visible:text-neutral-900 focus-visible:outline-hidden dark:bg-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-50/5 dark:hover:text-white dark:focus-visible:bg-neutral-50/10 dark:focus-visible:text-white" role="menuitem">Settings</a>
								<a href="#" class="bg-neutral-50 px-4 py-2 text-sm text-neutral-600 hover:bg-neutral-900/5 hover:text-neutral-900 focus-visible:bg-neutral-900/10 focus-visible:text-neutral-900 focus-visible:outline-hidden dark:bg-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-50/5 dark:hover:text-white dark:focus-visible:bg-neutral-50/10 dark:focus-visible:text-white" role="menuitem">Sign Out</a> --}}
							</div>
						</div>
					</div>
				</div>

				<template x-if="test.filePath">
					<div class="text-xs text-zinc-500 mt-1">
						<a
							:href="generateDeepLink(test.filePath, test.error ? test.error.line : (test.failure ? test.failure.line : 1))"
							class="inline-flex items-center gap-1 hover:text-zinc-300 underline"
							:title="'Open in ' + availableEditors[selectedEditor] + ' at line ' + (test.error ? test.error.line : (test.failure ? test.failure.line : 1))"
						>
							<span x-text="test.relativeFilePath + ':' + (test.error ? test.error.line : (test.failure ? test.failure.line : 1))"></span>
						</a>
					</div>
				</template>

				<template x-if="test.notes && test.notes.length > 0">
					<div class="prose prose-sm text-sm text-zinc-400 mt-2">
						<template x-for="note in test.notes" :key="note">
							<div x-html="note"></div>
						</template>
					</div>
				</template>

				<template x-if="test.error">
					<div class="mt-2 p-3 bg-red-950/20 border border-red-500/30">
						<div class="flex items-start gap-2">
							<div class="text-sm">
								<div class="font-bold text-red-400" x-text="test.error.message"></div>
								<div class="text-red-400" x-text="test.error.exceptionClass + ' on line ' + test.error.line"></div>
							</div>
						</div>
					</div>
				</template>

				<template x-if="test.failure">
					<div 
						x-data="{
							get message() {
								if (test.failure.exceptionClass) {
									return test.failure.exceptionClass + ' on line ' + (test.failure.line ?? '?');
								} else if (test.failure.line) {
									return 'Unknown failure on line ' + (test.failure.line ?? '?');
								} else {
									return 'Unknown failure';
								}
							},
						}"
						class="mt-2 p-3 bg-red-950/20 border border-red-500/30"
					>
						<div class="flex items-start gap-2">
							<div class="text-sm">
							<div class="font-bold text-red-400" x-text="test.failure"></div>
								<div class="font-bold text-red-400" x-text="test.failure.message"></div>
								<div class="text-red-400" x-text="message"></div>
							</div>
						</div>
					</div>
				</template>
			</div>

			<div class="flex-shrink-0 flex items-end gap-2">
				<div class="flex flex-wrap items-center gap-1 justify-end">
					<template x-if="test.prs">
						<template x-for="prNumber in test.prs" :key="prNumber">
							<a :href="generatePullRequestUrl(prNumber)" target="_blank" :title="'View PR #' + prNumber + ' on GitHub'"
								class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium text-gray-300 bg-zinc-900 border border-zinc-700 hover:bg-zinc-800">
								<span x-text="'PR #' + prNumber"></span>
							</a>
						</template>
					</template>
					<template x-if="test.assignees">
						<template x-for="assignee in test.assignees" :key="assignee">
							<span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium text-gray-300 bg-zinc-900 border border-zinc-700">
								<span x-text="assignee"></span>
							</span>
						</template>
					</template>
					<template x-if="test.issues">
						<template x-for="issueNumber in test.issues" :key="issueNumber">
							<a :href="generateIssueUrl(issueNumber)" target="_blank" :title="'View Issue #' + issueNumber + ' on GitHub'"
								class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium text-gray-300 bg-zinc-900 border border-zinc-700 hover:bg-zinc-800">
								<span x-text="'Issue #' + issueNumber"></span>
							</a>
						</template>
					</template>
					<template x-if="test.todo">
						<span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium text-amber-300 bg-amber-900/20 border border-amber-500/30">
							<span>WIP</span>
						</span>
					</template>
				</div>
			</div>
		</div>
	</div>
</div>