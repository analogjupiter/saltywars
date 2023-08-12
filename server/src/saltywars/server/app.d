/++
    saltywars server – Game server and extension host

    Copyright:  © 2023  Elias Batek
    License:    BSL-1.0
 +/
module saltywars.server.app;

import saltywars.launcher.extensions.host;
import std.stdio;

void main()
{
    writeln("Servus");

    auto xts = discoverExtensions();
    writeln("Base:  ", xts.base);
    writeln("Core:  ", xts.core);
    writeln("Addon: ", xts.addn);
}
