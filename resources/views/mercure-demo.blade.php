<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Mercure 1.0 + Laravel Broadcasting demo</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-50 text-gray-900">
        <div class="mx-auto max-w-3xl space-y-8 p-8">
            <header class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold">Mercure 1.0 + Laravel Broadcasting</h1>

                @auth
                    <form method="POST" action="/logout" class="flex items-center gap-3">
                        @csrf
                        <span class="text-sm text-gray-600">Logged in as <strong>{{ auth()->user()->name }}</strong></span>
                        <button type="submit" class="rounded bg-gray-200 px-3 py-1 text-sm hover:bg-gray-300">Log out</button>
                    </form>
                @else
                    <div class="flex items-center gap-2 text-sm">
                        Log in as:
                        @foreach ($users as $user)
                            <a href="/login-as/{{ $user->id }}" class="rounded bg-indigo-600 px-3 py-1 text-white hover:bg-indigo-500">{{ $user->name }}</a>
                        @endforeach
                    </div>
                @endauth
            </header>

            {{-- Public channel --}}
            <section class="rounded-lg border border-gray-200 bg-white p-5">
                <h2 class="text-lg font-medium">Public channel — "ticks"</h2>
                <p class="mt-1 text-sm text-gray-600">No subscriber cookie is fetched for this one: the hub accepts the subscribe request anonymously, and the update is published unprivated.</p>
                @auth
                    <button id="send-tick" class="mt-3 rounded bg-indigo-600 px-3 py-1.5 text-sm text-white hover:bg-indigo-500">Send a tick</button>
                @endauth
                <ul id="tick-log" class="mt-3 space-y-1 text-sm text-gray-700"></ul>
            </section>

            @auth
                {{-- Private channel --}}
                <section class="rounded-lg border border-gray-200 bg-white p-5">
                    <h2 class="text-lg font-medium">Private channel — "room.{{ auth()->id() }}"</h2>
                    <p class="mt-1 text-sm text-gray-600">Only a browser authorized (and cookied) for this exact user's room receives these. Log in as the other user in a second tab to see it <em>not</em> arrive there.</p>
                    <form id="message-form" class="mt-3 flex gap-2">
                        <select id="recipient" class="rounded border border-gray-300 px-2 py-1.5 text-sm">
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" @selected($user->id === auth()->id())>{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <input id="message-text" type="text" placeholder="Say something" class="flex-1 rounded border border-gray-300 px-2 py-1.5 text-sm" maxlength="255">
                        <button type="submit" class="rounded bg-indigo-600 px-3 py-1.5 text-sm text-white hover:bg-indigo-500">Send</button>
                    </form>
                    <ul id="message-log" class="mt-3 space-y-1 text-sm text-gray-700"></ul>
                </section>

                {{-- Presence channel --}}
                <section class="rounded-lg border border-gray-200 bg-white p-5">
                    <h2 class="text-lg font-medium">Presence channel — "lobby"</h2>
                    <p class="mt-1 text-sm text-gray-600">Built on the hub's subscription API: every connect/disconnect is itself an "active: true/false" update on a reserved topic, with your join payload attached.</p>
                    <p class="mt-3 text-sm font-medium">Here now:</p>
                    <ul id="presence-list" class="mt-1 space-y-1 text-sm text-gray-700"></ul>
                </section>
            @endauth
        </div>

        <script type="module">
            function logLine(listId, text) {
                const li = document.createElement("li");
                li.textContent = text;
                document.getElementById(listId).prepend(li);
            }

            document.getElementById("send-tick")?.addEventListener("click", () => {
                fetch("/demo/tick", {
                    method: "POST",
                    headers: { "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content },
                });
            });

            window.Echo.channel("ticks").listen(".Tick", (event) => {
                logLine("tick-log", `Tick at ${event.time}`);
            });

            @auth
                document.getElementById("message-form")?.addEventListener("submit", (event) => {
                    event.preventDefault();

                    const recipientId = document.getElementById("recipient").value;
                    const text = document.getElementById("message-text").value;

                    fetch("/demo/message", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({ recipient_id: recipientId, text }),
                    });

                    document.getElementById("message-text").value = "";
                });

                window.Echo.private("room.{{ auth()->id() }}").listen(".MessageSent", (event) => {
                    logLine("message-log", `${event.from}: ${event.text}`);
                });

                const presenceMembers = new Map();

                function renderPresence() {
                    const list = document.getElementById("presence-list");
                    list.innerHTML = "";
                    presenceMembers.forEach((member) => {
                        const li = document.createElement("li");
                        li.textContent = member.name;
                        list.appendChild(li);
                    });
                }

                // Members without an id are skipped: a subscriber that joined the
                // hub anonymously (possible when the hub runs with "anonymous")
                // has no payload, and this demo has nothing to display for it.
                window.Echo.join("lobby")
                    .here((members) => {
                        members.forEach((member) => member.id != null && presenceMembers.set(member.id, member));
                        renderPresence();
                    })
                    .joining((member) => {
                        if (member.id == null) return;
                        presenceMembers.set(member.id, member);
                        renderPresence();
                    })
                    .leaving((member) => {
                        presenceMembers.delete(member.id);
                        renderPresence();
                    });
            @endauth
        </script>
    </body>
</html>
