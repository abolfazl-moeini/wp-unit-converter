import { build } from "esbuild";
import path from "node:path";
import {
    importAsGlobals,
    saveAssetFile,
} from "@core/dependency-extraction-esbuild-plugin";

const result = await build({
    entryPoints: ["assets/dependencies.js"],
    bundle: true,
    minify: true,
    metafile: true,
    outfile: "assets/bundles/unit-converter-deps.js",
    loader: { ".js": "jsx", ".ts": "ts" },
    format: "iife",
    globalName: "WPDevUnitConverter",
    plugins: [importAsGlobals()],
});

saveAssetFile(result);
