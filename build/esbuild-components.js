import { build } from "esbuild";
import path from "node:path";
import { glob } from "glob";

import {
    importAsGlobals,
    saveAssetFile,
    writeFile,
} from "@core/dependency-extraction-esbuild-plugin";

const jsfiles = await glob("**/script.js", {
    ignore: ["node_modules/!**", "assets/!**"],
});

jsfiles.map(async (sourceFile) => {
    const isDev = false;

    console.info(`build:${sourceFile}`);

    const bundleFile = path.join(
        "assets/bundles",
        path.basename(path.dirname(sourceFile)) + ".js",
    );

    const internalItems = [];

    const result = await build({
        entryPoints: [sourceFile],
        bundle: true,
        minify: !isDev,
        sourcemap: isDev,
        metafile: true,
        define: { IS_DEV: String(isDev) },
        outfile: bundleFile,
        loader: { ".js": "jsx", ".ts": "ts" },
        plugins: [
            importAsGlobals(
                {
                    "@core/utils": "WPDevUnitConverter.utils",
                },
                internalItems,
            ),
        ],
    });

    console.info(`Done: ${bundleFile}`);

    saveAssetFile(result, ["unit-converter-deps"], internalItems);
});
