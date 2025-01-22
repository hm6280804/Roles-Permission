<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Users') }}
            </h2>
            @can('create users')
            <a href="{{ route('users.create') }}" class="bg-slate-700 text-sm rounded-md text-white px-3 py-2">Create</a>
            @endcan
        </div>
        
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-message></x-message>
            <p>Users list</p>
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr class="border-b">
                        <th class="px-6 py-5 text-left">#</th>
                        <th class="px-6 py-5 text-left">Name</th>
                        <th class="px-6 py-5 text-left">Email</th>
                        <th class="px-6 py-5 text-left">Roles</th>
                        <th class="px-6 py-5 text-left">created</th>
                        <th class="px-6 py-5 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @if ($users->isNotEmpty())
                       @foreach ($users as $user)
                        <tr class="border-b">
                                <td class="px-6 py-5 text-left">{{ $user->id }}</td>
                                <td class="px-6 py-5 text-left">{{ $user->name }}</td>
                                <td class="px-6 py-5 text-left">{{ $user->email }}</td>
                                <td class="px-6 py-5 text-left">{{ $user->roles->pluck('name')->implode(', ') }}</td>

                                <td class="px-6 py-5 text-left">{{ \Carbon\Carbon::parse($user->created_at)->format('d M, Y')}}</td>
                                <td class="px-6 py-5 text-center">
                                    @can('edit users')
                                    <a href="{{ route('users.edit', $user->id) }}" class="bg-slate-700 text-sm rounded-md text-black px-3 py-2 hover:bg-slate-600 mr-2">
                                        Edit
                                    </a>
                                    @endcan
                                    
                                    
                                    <a href="javascript:void(0);" onclick="deleteUser({{ $user->id }})" class="bg-red-700 text-sm rounded-md text-black px-3 py-2 hover:bg-red-600 ml-2">
                                        Delete 
                                    </a>
                                </td>
                            </tr>
                       @endforeach 
                    @endif
                    
                </tbody>
            </table>
            <div class="my-3">
                {{ $users->links() }}
            </div>
            
        </div>
    </div>
    <x-slot name="script">
        <script type="text/javascript">
            function deleteUser(id){
                
                if(confirm("Are you sure You want to delete")){
                    $.ajax({
                        url: '{{ route("users.destroy") }}',
                        type: 'delete',
                        data: {id:id},
                        dataType: 'json',
                        headers: {
                            'x-csrf-token' : '{{ csrf_token() }}'
                        },
                        success: function(response){
                            window.location.href = '{{ route("users.index") }}'
                        }
                    })
                }
            }
        </script>
    </x-slot>
</x-app-layout>
