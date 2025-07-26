<div class="bg-gray-900 text-white min-h-screen pt-8">
    <div class="container mx-auto p-4">
        <!-- Ventana principal -->
        <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden border border-gray-700">
            <!-- Barra de herramientas superior -->
            <div class="bg-gray-700 px-4 py-2 flex items-center">
                <button wire:click='guardar_archivo' class="cursor-pointer mr-4 text-gray-300 hover:text-white hover:bg-blue-950">Archivo</button>
                {{-- <button class="text-gray-300 hover:text-white">Ayuda</button> --}}
            </div>

            <!-- Títulos de las secciones -->
            <div class="flex justify-between px-4 py-2 bg-gray-700 border-b border-gray-600">
                <h3 class="text-lg font-medium">Área de edición</h3>
                <h3 class="text-lg font-medium">Código Compilado</h3>
            </div>

            <div class="flex">
                <!-- Área de edición -->
                <div class="w-1/2 p-4 border-r border-gray-600">
                    <textarea wire:keydown.debounce.1500ms='tabla_base' wire:model='textoPlano' id="editor" rows="22"
                        class="w-full h-full bg-gray-800 border-2 border-gray-600 rounded-md p-3 focus:outline-none resize-none text-white placeholder-gray-500"
                        placeholder="Escribe tu código aquí..."></textarea>
                </div>

                <!-- Código compilado -->
                <div class="w-1/2 p-4">
                    <pre class="h-full w-full border-2 border-gray-600 rounded-md p-3 overflow-auto bg-gray-900">
                        <code id="compiledCode" class="text-gray-300">{!! $code !!}</code>
                    </pre>
                </div>
            </div>

            <!-- Estadísticas inferiores -->
            <div class="flex justify-between px-4 py-2 bg-gray-700 border-t border-gray-600">
                <div class="flex space-x-4">
                    <span>Palabras Reservadas: <span id="reservedWords"
                            class="text-gray-400">{{ $grupo6 }}</span></span>
                    <span>Operadores: <span id="operators" class="text-gray-400">{{ $grupo5 }}</span></span>
                    <span>Comentarios: <span id="comments" class="text-gray-400">{{ $grupo1 + $grupo2 }}</span></span>
                </div>
            </div>
            <div class="mt-4">
                @if (count($errors))
                    <div class="text-red-500 p-4 border border-red-700 bg-red-50">
                        <h3 class="font-bold mb-2">Errores léxicos:</h3>
                        <ul class="list-disc pl-5">
                            @foreach ($errors as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
        @if (!empty($tablaSimbolos))
            <h2 class="text-xl font-bold text-white mt-6 mb-2">Tabla de Símbolos</h2>
            <table class="w-full text-sm text-left text-white border border-gray-700">
                <thead class="bg-gray-700 text-gray-300">
                    <tr>
                        <th class="px-4 py-2 border border-gray-600">Lexema</th>
                        <th class="px-4 py-2 border border-gray-600">Línea</th>
                    </tr>
                </thead>
                <tbody class="bg-gray-800">
                    @foreach ($tablaSimbolos as $simbolo)
                        <tr>
                            <td class="px-4 py-2 border border-gray-600">{{ $simbolo['lexema'] }}</td>
                            <td class="px-4 py-2 border border-gray-600">{{ $simbolo['linea'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @if ($guardarArchivo)
            @include('modal')
        @endif

        {{-- //{{ print_r($tablaSimbolos) }} --}}
    </div>
</div>
