<div>
    @if($idea->meetings()->where('is_finished', false)->count() == 0)
        <x-button gray label="New meeting" wire:click="newMeeting" class="float-right" />
    @else
        <x-button green label="Go to open meeting" href="/ideas/{{ $idea->id }}/live_meeting" class="float-right" />
    @endif
    <h2 class="text-lg mb-4">Meetings</h2>
    <div class="px-4 sm:px-6 lg:px-8 bg-white">
        <div class="flow-root">
            <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead>
                            <tr>
                                <th scope="col" class="py-3.5 pr-3 pl-4 text-left text-sm font-semibold text-gray-900 sm:pl-0">Date</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Title</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Description</th>
                                <th scope="col" class="relative py-3.5 pr-4 pl-3 sm:pr-0">
                                    <span class="sr-only">Edit</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($idea->meetings()->where('is_finished', 1)->get() as $meeting)
                                <tr>
                                    <td class="py-4 pr-3 pl-4 text-sm font-medium whitespace-nowrap text-gray-900 sm:pl-0">{{ $meeting->created_at }}</td>
                                    <td class="px-3 py-4 text-sm whitespace-nowrap text-gray-500">{{ $meeting->title }}</td>
                                    <td class="px-3 py-4 text-sm whitespace-nowrap text-gray-500">{{ $meeting->description }}</td>
                                    <td class="relative py-4 pr-4 pl-3 text-right text-sm font-medium whitespace-nowrap sm:pr-0">
                                        <a href="/meetings/{{ $meeting->id }}" class="text-indigo-600 hover:text-indigo-900">See<span class="sr-only"></span></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>