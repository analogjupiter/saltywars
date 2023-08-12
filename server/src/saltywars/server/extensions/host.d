/++
    Extension host implementation

    Copyright:  © 2023  Elias Batek
    License:    BSL-1.0
 +/
module saltywars.launcher.extensions.host;

import saltywars.server.env;
import saltywars.server.extensions.info;

import std.algorithm : filter, map;
import std.path : buildNormalizedPath;
import std.range : array;

@safe:

struct Host
{
}

struct ExtensionSets
{
    HostExtensionInfo[] core;
    HostExtensionInfo[] base;
    HostExtensionInfo[] addn;

    int opApply(scope int delegate(ref HostExtensionInfo) @safe dg)
    {
        int result = 0;

        foreach (item; this.core)
        {
            result = dg(item);
            if (result)
                break;
        }

        foreach (item; this.base)
        {
            result = dg(item);
            if (result)
                break;
        }

        foreach (item; this.addn)
        {
            result = dg(item);
            if (result)
                break;
        }

        return result;
    }
}

string[] discoverExtensions(string repo)
{
    static string[] discoverExtensionsIn(const string dir) @trusted
    {
        import std.file : dirEntries, DirEntry, SpanMode;

        return dir.dirEntries(SpanMode.shallow)
            .filter!((DirEntry e) => e.isDir && e.isExtensionDir)
            .map!(e => e.name)
            .array;
    }

    string extensionsDir = env("SALTYWARS_XT_DIR");
    return discoverExtensionsIn(extensionsDir.buildNormalizedPath(repo));
}

ExtensionSets discoverExtensions()
{
    string[] corePaths = discoverExtensions("core");
    string[] basePaths = discoverExtensions("base");
    string[] addnPaths = discoverExtensions("addon");

    // dfmt off
    HostExtensionInfo[] coreXTs = corePaths.map!(xtDir => loadHostExtensionInfo(Repository.core, xtDir)).array;
    HostExtensionInfo[] baseXTs = basePaths.map!(xtDir => loadHostExtensionInfo(Repository.base, xtDir)).array;
    HostExtensionInfo[] addnXTs = addnPaths.map!(xtDir => loadHostExtensionInfo(Repository.addn, xtDir)).array;
    // dfmt on

    return ExtensionSets(
        coreXTs,
        baseXTs,
        addnXTs,
    );
}

struct Communicator
{
}
