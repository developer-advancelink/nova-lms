{{--
    SHARED DESTRUCTIVE-ACTION CONFIRMATION — admin screens

    Replaces onsubmit="return confirm(...)". A native confirm() returns false
    whenever dialogs are blocked — in an embedded browser, or after Chrome's
    "don't allow this page to create more dialogs" — and the form then silently
    never submits, so the button appears dead. This is an in-page modal built
    the same way as the one in pages/lecturer/courses/index.blade.php, so it
    works regardless of dialog policy.

    Usage: put this inside an element carrying the Alpine scope below, and have
    each trigger call ask(). Always pass @js(...) so names containing an
    apostrophe cannot break out of the attribute.

      <div x-data="{
          confirmOpen: false, confirmAction: '', confirmTitle: '',
          confirmBody: '', confirmCta: 'Delete',
          ask(action, title, body, cta = 'Delete') {
              this.confirmAction = action; this.confirmTitle = title;
              this.confirmBody = body; this.confirmCta = cta;
              this.confirmOpen = true;
          },
          closeConfirm() { this.confirmOpen = false; }
      }" @keydown.escape.window="closeConfirm()">

          <button type="button"
                  @click="ask(@js(route('admin.users.destroy', $user)),
                              @js('Delete '.$user->name.'?'),
                              @js('This cannot be undone.'))">Delete</button>

          @include('pages.admin._confirm-delete')
      </div>
--}}

{{-- BACKDROP --}}
<div x-cloak
     x-show="confirmOpen"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click="closeConfirm()"
     class="fixed inset-0 bg-[#0F1B2D] bg-opacity-60 z-40"
     style="display:none;"></div>

{{-- PANEL --}}
<div x-cloak
     x-show="confirmOpen"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 scale-95"
     x-transition:enter-end="opacity-100 scale-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100 scale-100"
     x-transition:leave-end="opacity-0 scale-95"
     class="fixed inset-0 z-50 flex items-center justify-center p-6 pointer-events-none"
     style="display:none;">

    <div @click.stop
         class="bg-white rounded-2xl border border-[#C8D3E0] shadow-2xl w-full max-w-md pointer-events-auto overflow-hidden">

        <div class="px-6 py-4 border-b border-[#E8EEF4]">
            <h3 class="font-semibold text-slate-900" x-text="confirmTitle"></h3>
        </div>

        <div class="px-6 py-5">
            <p class="text-sm text-slate-600" x-text="confirmBody"></p>
        </div>

        <div class="px-6 py-4 border-t border-[#E8EEF4] flex items-center justify-end gap-3">
            <button type="button"
                    @click="closeConfirm()"
                    class="rounded-lg border border-[#C8D3E0] text-slate-600 text-sm font-medium px-4 py-2 hover:bg-[#E8EEF4] transition-colors">
                Cancel
            </button>

            <form method="POST" :action="confirmAction">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="rounded-lg bg-rose-600 text-white text-sm font-medium px-4 py-2 hover:bg-rose-700 transition-colors"
                        x-text="confirmCta"></button>
            </form>
        </div>
    </div>
</div>
