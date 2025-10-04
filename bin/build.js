#!/usr/bin/env node

import * as esbuild from 'esbuild'
import { copyFileSync, existsSync, mkdirSync } from 'fs'
import { dirname } from 'path'

const isDev = process.argv.includes('--dev')

async function compile(options) {
    const context = await esbuild.context(options)

    if (isDev) {
        await context.watch()
    } else {
        await context.rebuild()
        await context.dispose()
    }
}

const defaultOptions = {
    define: {
        'process.env.NODE_ENV': isDev ? `'development'` : `'production'`,
    },
    bundle: true,
    mainFields: ['module', 'main'],
    platform: 'neutral',
    sourcemap: isDev ? 'inline' : false,
    sourcesContent: isDev,
    treeShaking: true,
    target: ['es2020'],
    minify: !isDev,
    plugins: [{
        name: 'watchPlugin',
        setup(build) {
            build.onStart(() => {
                console.log(`Build started at ${new Date(Date.now()).toLocaleTimeString()}: ${build.initialOptions.outfile}`)
            })

            build.onEnd((result) => {
                if (result.errors.length > 0) {
                    console.log(`Build failed at ${new Date(Date.now()).toLocaleTimeString()}: ${build.initialOptions.outfile}`, result.errors)
                } else {
                    console.log(`Build finished at ${new Date(Date.now()).toLocaleTimeString()}: ${build.initialOptions.outfile}`)
                }
            })
        }
    }],
}

// Ensure dist directory exists
const distDir = 'resources/dist'
if (!existsSync(distDir)) {
    mkdirSync(distDir, { recursive: true })
}

// Copy CSS file (no compilation needed)
const cssSource = 'resources/css/filament-forum.css'
const cssDest = 'resources/dist/filament-forum.css'

if (existsSync(cssSource)) {
    copyFileSync(cssSource, cssDest)
    console.log('✓ CSS copied to dist/filament-forum.css')
} else {
    console.error('✗ CSS source file not found')
}

// Compile JavaScript with esbuild
compile({
    ...defaultOptions,
    entryPoints: ['./resources/js/components/forum-mentions.js'],
    outfile: './resources/dist/forum-mentions.js',
})

console.log('Build complete!')
