<?php

namespace App\Filament\Tutor\Pages;

use App\Models\Organization as OrganizationModel;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class Organization extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $slug = 'organization';

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-building-office';

    protected string $view = 'filament.tutor.pages.organization';

    protected static ?int $navigationSort = 1;

    public OrganizationModel $organization;

    public ?array $data = [];

    public static function getNavigationGroup(): string
    {
        return __('tutor.nav.settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('tutor.nav.organization');
    }

    public function getTitle(): string
    {
        return __('tutor.nav.organization');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->organization()->exists() ?? false;
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->organization()->exists() ?? false;
    }

    public function mount(): void
    {
        $this->organization = auth()->user()->organization;

        abort_if(! $this->organization, 404);

        $this->form->model($this->organization);

        $this->form->fill($this->organization->only([
            'name',
            'slug',
            'description',
            'website',
            'category',
            'founded',
            'position',
            'is_active',
        ]));
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->model($this->organization)
            ->statePath('data')
            ->components([
                Section::make(__('tutor.form.organization_logo'))
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('logo')
                            ->collection('logo')
                            ->label(__('tutor.form.organization_logo'))
                            ->image()
                            ->imageEditor(),
                        SpatieMediaLibraryFileUpload::make('stamp')
                            ->label(__('tutor.form.organization_stamp'))
                            ->collection('stamp')
                            ->image()
                            ->imageEditor(),
                    ]),
                Section::make(__('tutor.form.organization_information'))
                    ->description(__('tutor.form.organization_info_desc'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('tutor.form.organization_name'))
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('slug', Str::slug($state));
                            }),

                        TextInput::make('slug')
                            ->required()
                            ->unique(
                                table: 'organizations',
                                column: 'slug',
                                ignorable: fn () => $this->organization,
                            ),

                        Textarea::make('description')
                            ->label(__('tutor.form.description'))
                            ->rows(5)
                            ->columnSpanFull(),

                        Toggle::make('is_active')
                            ->label(__('tutor.form.active'))
                            ->default(true),
                    ])
                    ->columns(2),

                Section::make(__('tutor.form.organization_public_profile'))
                    ->description(__('tutor.form.organization_public_profile_desc'))
                    ->schema([
                        TextInput::make('website')
                            ->label(__('tutor.form.website'))
                            ->url()
                            ->maxLength(255)
                            ->placeholder('https://example.com'),

                        TextInput::make('category')
                            ->label(__('tutor.form.category'))
                            ->maxLength(255)
                            ->placeholder(__('tutor.form.organization_category_placeholder')),

                        TextInput::make('founded')
                            ->label(__('tutor.form.founded'))
                            ->numeric()
                            ->minValue(1800)
                            ->maxValue((int) date('Y')),
                        TextInput::make('position')
                            ->label(__('tutor.form.position'))
                            ->maxLength(255)
                            ->placeholder(__('tutor.form.position_placeholder')),
                    ])
                    ->columns(2),
            ]);
    }

    public function save(): void
    {
        $organization = auth()->user()->organization;

        abort_if(! $organization, 404);

        $data = $this->form->getState();

        $organization->update([
            'name' => $data['name'],
            'slug' => Str::slug($data['slug']),
            'description' => $data['description'],
            'website' => $data['website'] ?? null,
            'category' => $data['category'] ?? null,
            'founded' => $data['founded'] ?? null,
            'position' => $data['position'] ?? null,
            'is_active' => $data['is_active'],
        ]);

        $this->form->model($organization)->saveRelationships();

        $this->organization = $organization->fresh();

        $this->form->model($this->organization)->fill($this->organization->only([
            'name',
            'slug',
            'description',
            'website',
            'category',
            'founded',
            'position',
            'is_active',
        ]));

        Notification::make()
            ->success()
            ->title(__('tutor.notifications.organization_updated'))
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            // Action::make('save')
            //     ->label(__('tutor.form.save'))
            //     ->color('primary')
            //     ->action(fn () => $this->save()),
        ];
    }
}
