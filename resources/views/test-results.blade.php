<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
	<script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/focus@3.x.x/dist/cdn.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/@ryangjchandler/alpine-clipboard@2.x.x/dist/alpine-clipboard.js" defer></script>
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.13.3/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@marcreichel/alpine-auto-animate@latest/dist/alpine-auto-animate.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        body {
            font-family: 'JetBrains Mono', monospace;
        }
        .prose {
            color: inherit;
        }
        .prose code {
            color: #f0f0f0;
        }
        .prose a {
            color: inherit;
        }
    </style>
</head>
<body class="h-full bg-black text-gray-300">
    <div x-data="testResultsApp()" x-cloak class="h-full w-full flex flex-col">
        <!-- Header -->
        <header class="border-b border-zinc-800 px-4 py-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <h1 class="text-lg font-semibold text-white">
                        <span x-text="filteredCounts.tests"></span> Tests
                    </h1>
                    <div class="flex items-center gap-3">
                        <div class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm font-medium text-white" x-text="filteredCounts.success"></span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm font-medium text-white" x-text="filteredCounts.skipped"></span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm font-medium text-white" x-text="filteredCounts.failed"></span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <select x-model="groupBy" class="bg-zinc-900 border border-zinc-700 text-white px-2 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-green-500">
                        <option value="suite">Group by Suite</option>
                        <option value="file">Group by File</option>
                    </select>
                    <select x-model="sortBy" class="bg-zinc-900 border border-zinc-700 text-white px-2 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-green-500">
                        <option value="name">Sort by Name</option>
                        <option value="total">Sort by Total Tests</option>
                        <option value="percentage">Sort by Percentage</option>
                    </select>
                    <button @click="sortDirection = sortDirection === 'asc' ? 'desc' : 'asc'" class="inline-flex items-center gap-2 px-3 py-1 text-sm font-medium text-gray-300 bg-black border border-zinc-700 hover:bg-zinc-900 focus:outline-none focus:ring-1 focus:ring-green-500">
                        <span x-text="sortDirection === 'asc' ? '↑' : '↓'"></span>
                    </button>
                    
                    <!-- Editor Selection Dropdown -->
                    <div x-data="{ open: false }" class="relative">
                        <button
                            @click="open = !open"
                            class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-gray-300 bg-black border border-zinc-700 hover:bg-zinc-900 focus:outline-none focus:ring-1 focus:ring-green-500"
                        >
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 00-1.414-1.414z" clip-rule="evenodd"></path>
                            </svg>
                            <span x-text="availableEditors[selectedEditor] || 'Editor'"></span>
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                        <div
                            x-show="open"
                            @click.away="open = false"
                            x-transition
                            class="absolute right-0 mt-2 w-48 bg-black border border-zinc-700 shadow-lg z-10"
                        >
                            <template x-for="(editorName, editorKey) in availableEditors" :key="editorKey">
                                <button
                                    @click="selectedEditor = editorKey; open = false"
                                    class="w-full text-left px-4 py-2 text-sm text-gray-300 hover:bg-zinc-900 flex items-center gap-2"
                                    :class="{ 'bg-zinc-800': selectedEditor === editorKey }"
                                >
                                    <svg x-show="selectedEditor === editorKey" class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <div x-show="selectedEditor !== editorKey" class="w-4"></div>
                                    <span x-text="editorName"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2 mt-2">
                <input x-model="search" type="text" placeholder="Search..." class="flex-grow bg-zinc-900 border border-zinc-700 text-white px-2 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-green-500">
                <select x-model="filters.status" class="bg-zinc-900 border border-zinc-700 text-white px-2 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-green-500">
                    <option value="all">All Statuses</option>
                    <option value="success">Passed</option>
                    <option value="failed">Failed</option>
                    <option value="skipped">Skipped</option>
                </select>
                <select x-model="filters.pr" class="bg-zinc-900 border border-zinc-700 text-white px-2 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-green-500">
                    <option value="all">All PRs</option>
                    <option value="yes">Has PR</option>
                    <option value="no">No PR</option>
                </select>
                <select x-model="filters.issue" class="bg-zinc-900 border border-zinc-700 text-white px-2 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-green-500">
                    <option value="all">All Issues</option>
                    <option value="yes">Has Issue</option>
                    <option value="no">No Issue</option>
                </select>
                <select x-model="filters.assignee" class="bg-zinc-900 border border-zinc-700 text-white px-2 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-green-500">
                    <option value="all">All Assignees</option>
                    <option value="none">None</option>
                    <template x-for="assignee in allAssignees" :key="assignee">
                        <option :value="assignee" x-text="assignee"></option>
                    </template>
                </select>
                <input x-model="filters.classPath" type="text" placeholder="Class path prefix..." class="bg-zinc-900 border border-zinc-700 text-white px-2 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-green-500">
            </div>
        </header>

        <!-- Test Results -->
        <div class="flex-1 overflow-y-auto p-4 space-y-6" x-auto-animate>
            <template x-for="group in processedGroups" :key="group.name">
                <div class="space-y-2">
                    <!-- Suite Header -->
					<template x-if="group.tests.length > 0">
						<div class="border-b border-zinc-800 pb-2 mb-2">
							<div class="flex items-center justify-between">
								<h2 class="text-sm font-bold text-white uppercase" x-text="group.name"></h2>
								<span class="text-sm font-medium text-white" x-text="`${group.filteredCount} / ${group.totalCount} tests`"></span>
							</div>
						</div>
					</template>

					<template x-if="group.tests.length > 0">
						<!-- Test Cases -->
						<div class="space-y-3" x-auto-animate >
							<template x-for="test in group.tests" :key="test.name">
								@include('pest-reports::test-result')
							</template>
						</div>
					</template>
                </div>
            </template>
        </div>
    </div>

    <script>
        function testResultsApp() {
            return {
                allTestSuites: @json($testSuites),
                selectedEditor: '{{ $selectedEditor }}',
                availableEditors: @json($availableEditors),
                search: '',
                groupBy: 'suite',
                sortBy: 'name',
                sortDirection: 'asc',
                filters: {
                    status: 'all',
                    pr: 'all',
                    issue: 'all',
                    assignee: 'all',
                    classPath: '',
                },
                
                get allAssignees() {
                    const assignees = new Set();
                    this.allTestSuites.forEach(suite => {
                        Object.values(suite.tests).forEach(test => {
                            if (test.assignees) {
                                test.assignees.forEach(assignee => assignees.add(assignee));
                            }
                        });
                    });
                    return Array.from(assignees).sort();
                },
                
                get processedGroups() {
                    const groups = {};
                    const searchLower = this.search.toLowerCase();

                    this.allTestSuites.forEach(suite => {
                        Object.values(suite.tests).forEach(test => {
                            const groupName = this.groupBy === 'file' ? test.relativeFilePath : suite.description;
                            if (!groups[groupName]) {
                                groups[groupName] = { name: groupName, tests: [], totalCount: 0 };
                            }
                            groups[groupName].totalCount++;

                            const status = test.error || test.failure ? 'failed' : (test.skipped ? 'skipped' : 'success');

                            if (this.filters.status !== 'all' && status !== this.filters.status) return;
                            if (this.filters.pr === 'yes' && !test.prs?.length) return;
                            if (this.filters.pr === 'no' && test.prs?.length) return;
                            if (this.filters.issue === 'yes' && !test.issues?.length) return;
                            if (this.filters.issue === 'no' && test.issues?.length) return;
                            if (this.filters.assignee !== 'all' && (this.filters.assignee === 'none' ? test.assignees?.length : !test.assignees?.includes(this.filters.assignee))) return;
                            if (this.filters.classPath && !test.filePath.startsWith(this.filters.classPath)) return;

                            const content = [
                                test.test,
                                test.filePath,
                                test.error?.message,
                                test.failure?.message,
                                ...(test.notes || [])
                            ].join(' ').toLowerCase();

                            if (searchLower && !content.includes(searchLower)) return;

                            groups[groupName].tests.push(test);
                        });
                    });

                    let sortedGroups = Object.values(groups).map(group => ({
                        ...group,
                        filteredCount: group.tests.length,
                        percentage: group.totalCount > 0 ? (group.tests.length / group.totalCount) * 100 : 0,
                    }));

                    sortedGroups.sort((a, b) => {
                        let compareA, compareB;
                        switch (this.sortBy) {
                            case 'total':
                                compareA = a.totalCount;
                                compareB = b.totalCount;
                                break;
                            case 'percentage':
                                compareA = a.percentage;
                                compareB = b.percentage;
                                break;
                            default: // name
                                compareA = a.name.toLowerCase();
                                compareB = b.name.toLowerCase();
                                break;
                        }
                        
                        if (compareA < compareB) return this.sortDirection === 'asc' ? -1 : 1;
                        if (compareA > compareB) return this.sortDirection === 'asc' ? 1 : -1;
                        return 0;
                    });
                    
                    return sortedGroups;
                },

                get filteredCounts() {
                    let counts = { tests: 0, success: 0, failed: 0, skipped: 0 };
                    this.processedGroups.forEach(group => {
                        group.tests.forEach(test => {
                            counts.tests++;
                            if (test.error || test.failure) counts.failed++;
                            else if (test.skipped) counts.skipped++;
                            else counts.success++;
                        });
                    });
                    return counts;
                },

                generateDeepLink(filePath, lineNumber) {
                    const editor = this.selectedEditor;
                    switch (editor) {
                        case 'phpstorm': return `phpstorm://open?file=${filePath}&line=${lineNumber}`;
                        case 'vscode': return `vscode://file/${filePath}:${lineNumber}`;
                        case 'sublime': return `subl://${filePath}:${lineNumber}`;
                        case 'vim': return `vim://${filePath}:${lineNumber}`;
                        default: return `phpstorm://open?file=${filePath}&line=${lineNumber}`;
                    }
                },

                generatePullRequestUrl(prNumber) {
                    const repository = '{{ $gitHubService->getRepository() }}';
                    if (repository) return `https://github.com/${repository}/pull/${prNumber}`;
                    return null;
                },

                generateIssueUrl(issueNumber) {
                    const repository = '{{ $gitHubService->getRepository() }}';
                    if (repository) return `https://github.com/${repository}/issues/${issueNumber}`;
                    return null;
                },

				generateLinearUrl(test) {
					const base = 'https://linear.new';
					let description = test.failure.message + ' on line ' + (test.failure.line ?? '?');

					if (test.failure?.message) {
						description += '\n\n' + test.failure.message;
					}

					if (test.error?.message) {
						description += '\n\n' + test.error.message;
					}

					if (test.failure?.exceptionClass) {
						description += '\n\n' + test.failure.exceptionClass;
					}
					
					if (test.failure?.line) {
						description += '\n\n' + 'Line ' + test.failure.line;
					}

					description = description.substring(0, 1000);
					description = encodeURIComponent(description);

					return `${base}?title=${encodeURIComponent(test.test)}&description=${description}`;
				},

				async generateIssue(test) {
					const response = await fetch('/pest-api/generate-issue', {
						method: 'POST',
						body: JSON.stringify({ test }),
					});

					const issue = await response.json().then(data => {
						return data.issue;
					});

					const url = `https://linear.new?title=${encodeURIComponent(issue.title)}&description=${encodeURIComponent(issue.description)}`;

					window.open(url, '_blank');
				},

				async runAgent(test, id = null) {
					if (!id) {
						id = Math.random().toString(36).substring(2, 15);
					}

					const response = await fetch('/pest-api/run-agent', {
						method: 'POST',
						body: JSON.stringify({ test, id }),
					});
					
					const result = await response.json();

					if (result.report.status === 'running') {
						return new Promise((resolve, reject) => {
							setTimeout(async () => {
								const result = await this.runAgent(test, id);
								resolve(result);
							}, 1000);
						});
					}

					return result;
				},

				composePrompt(test, task = 'Analyze the Pest test case and respond accordingly.') {
					return `<instructions>
	You are a Laravel developer looking at a Pest test case.
	You are given a test case and a test result.

	Analyze what the best thing to do is in relation to the content of the test case and the test result.
	Fix broken tests, improve test cases, add more tests, etc.

	It is also perfectly fine to do nothing in which case you should just return "Nothing to do".
</instructions>

<test-result>
${JSON.stringify(test, null, 2)}
</test-result>

<task>${task}</task>
					`;
				},
			};
        }
    </script>
</body>
</html>
