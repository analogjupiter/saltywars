/++
    Environment variable functions

    Copyright:  © 2023  Elias Batek
    License:    BSL-1.0
 +/
module saltywars.server.env;

import std.conv : to;
import std.process;

string env(string name, string defaultValue = null) @safe
{
    return environment.get(name, defaultValue);
}

T envAs(T)(string name, T defaultValue = T.init)
{
    string value = env(name, null);

    // not found?
    if (value is null)
        return defaultValue;

    return value.to!T();
}
