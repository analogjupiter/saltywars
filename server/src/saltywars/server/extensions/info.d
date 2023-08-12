/++
    Extension info incl. loading

    Copyright:  © 2023  Elias Batek
    License:    BSL-1.0
 +/
module saltywars.server.extensions.info;

import asdf;
import std.file : exists, readText;

@safe:

enum Repository
{
    none = 0,
    core,
    base,
    addn,

    addon = addn,
}

enum extensionInfoFilename = "xt.json";

string buildExtensionInfoPath(string dir)
{
    import std.path : buildNormalizedPath;

    return dir.buildNormalizedPath(extensionInfoFilename);
}

bool isExtensionDir(string path)
{
    return buildExtensionInfoPath(path).exists();
}

struct ExtensionInfo
{
    string name;

    @serdeKeys("version") string version_;

    string copyright;

    string exec;
}

ExtensionInfo loadExtensionInfo(string extensionDir)
{
    immutable string infoFile = buildExtensionInfoPath(extensionDir);
    immutable string infoRaw = infoFile.readText();

    return (function(immutable string infoRaw) @trusted {
        return deserialize!ExtensionInfo(infoRaw);
    })(infoRaw);

}

struct HostExtensionInfo
{
    Repository repo;
    string path;
    ExtensionInfo info;
}

HostExtensionInfo loadHostExtensionInfo(Repository repo, string extensionDir)
{
    auto info = loadExtensionInfo(extensionDir);

    return HostExtensionInfo(
        repo,
        extensionDir,
        info,
    );
}
