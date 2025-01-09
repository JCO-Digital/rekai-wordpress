const path = require("node:path");
const fs = require("node:fs");

/**
 * Converts a glob pattern to a RegExp
 * @param {string} glob - The glob pattern (e.g. "*.zip")
 * @returns {RegExp} - Regular expression that matches the glob pattern
 */
function globToRegExp(glob) {
    // Escape special regex characters except * and ?
    const escaped = glob.replace(/[.+^${}()|[\]\\]/g, '\\$&');

    // Convert glob wildcards to regex patterns
    const pattern = escaped
        .replace(/\*/g, '.*')     // * matches any number of characters
        .replace(/\?/g, '.');     // ? matches exactly one character

    // Create regex that matches the entire string
    return new RegExp(`^${pattern}$`);
}

/**
 * Tests if a filepath matches a glob pattern
 * @param {string} filepath - The filepath to test
 * @param {string} pattern - The glob pattern to match against
 * @returns {boolean} - Whether the filepath matches the pattern
 */
function matchGlobPattern(filepath, pattern) {
    const regex = globToRegExp(pattern);
    return regex.test(filepath);
}

module.exports = function (grunt) {
    "use strict";

    const distFile = fs.readFileSync(".distignore").toString()
    const ignoredFiles = distFile.split("\n").map(line => line.trim().replaceAll("\r", "")).filter(line => line.length > 0 || line.startsWith("#"))

    // Project configuration
    grunt.initConfig({
        pkg: grunt.file.readJSON("package.json"),

        addtextdomain: {
            options: {
                textdomain: "rekai-wordpress",
            },
            update_all_domains: {
                options: {
                    updateDomains: true,
                },
                src: [
                    "*.php",
                    "**/*.php",
                    "!.git/**/*",
                    "!bin/**/*",
                    "!node_modules/**/*",
                    "!tests/**/*",
                    "!vendor/**/*",
                    "!vendor-prefixed/**/*",
                ],
            },
        },

        wp_readme_to_markdown: {
            your_target: {
                files: {
                    "README.md": "readme.txt",
                },
            },
        },

        makepot: {
            target: {
                options: {
                    domainPath: "/languages",
                    exclude: [
                        ".git/*",
                        "bin/*",
                        "node_modules/*",
                        "tests/*",
                        "vendor/*",
                        "vendor-prefixed/*",
                    ],
                    mainFile: "rekai-wordpress.php",
                    potFilename: "rekai-wordpress.pot",
                    potHeaders: {
                        poedit: true,
                        "x-poedit-keywordslist": true,
                    },
                    type: "wp-plugin",
                    updateTimestamp: true,
                },
            },
        },

        zip: {
            'skip-files': {
                router: function (filepath) {
                    const isIgnored = ignoredFiles.filter(f => {
                        // Don't zip files that are ignored by .distignore also run a regex
                        const match = matchGlobPattern(filepath, f)
                        return filepath.startsWith(f) || filepath.includes(f) || match
                    })
                    if (isIgnored.length > 0) return null
                    return filepath
                },
                src: ['**/*'],
                dest: 'build/rekai-wordpress.zip'
            },
        }
    });

    grunt.loadNpmTasks("grunt-wp-i18n");
    grunt.loadNpmTasks("grunt-wp-readme-to-markdown");
    grunt.loadNpmTasks("grunt-zip")
    grunt.registerTask("default", ["i18n", "readme"]);
    grunt.registerTask("release", ["i18n", "readme", "zip"])
    grunt.registerTask("i18n", ["addtextdomain", "makepot"]);
    grunt.registerTask("readme", ["wp_readme_to_markdown"]);

    grunt.util.linefeed = "\n";
}
