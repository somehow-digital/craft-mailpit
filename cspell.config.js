export default {
	dictionaries: ['project'],
	dictionaryDefinitions: [
		{
			addWords: true,
			name: 'project',
			path: './docs/dictionary.txt',
		},
	],
	import: ['@somehow-digital/cspell-dictionary'],
	ignorePaths: ['docs/.astro/', 'docs/.void/', 'docs/dist/'],
	language: 'en',
	version: '0.2',
};
