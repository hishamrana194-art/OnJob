@extends('layouts.app')

@section('title', 'Create Profile - OnJob')

@section('styles')
<style>
    .profile-card {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        padding: 2rem;
    }
    .profile-header {
        border-bottom: 2px solid #0d6efd;
        padding-bottom: 1rem;
        margin-bottom: 2rem;
    }
    .country-select-wrapper {
        position: relative;
    }
    #country-select {
        max-height: 200px;
        overflow-y: auto;
    }
    .photo-preview {
        max-width: 200px;
        max-height: 200px;
        margin-top: 1rem;
        border-radius: 0.5rem;
        display: none;
    }
</style>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="profile-card">
            <div class="profile-header">
                <h2 class="mb-0">
                    <i class="fas fa-user-circle text-primary"></i>
                    {{ isset($profile) ? 'Edit Profile' : 'Create Profile' }}
                </h2>
                <p class="text-muted mb-0 mt-2">Fill in your professional information</p>
            </div>

            <form action="{{ route('profile.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <!-- First Name -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="frist_name" class="form-label">
                                <i class="fas fa-user"></i> First Name <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="frist_name"
                                   id="frist_name"
                                   class="form-control @error('frist_name') is-invalid @enderror"
                                   placeholder="Enter first name"
                                   value="{{ old('frist_name', $profile->frist_name ?? '') }}"
                                   required>
                            @error('frist_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Last Name -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="last_name" class="form-label">
                                <i class="fas fa-user"></i> Last Name <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="last_name"
                                   id="last_name"
                                   class="form-control @error('last_name') is-invalid @enderror"
                                   placeholder="Enter last name"
                                   value="{{ old('last_name', $profile->last_name ?? '') }}"
                                   required>
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Age -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="age" class="form-label">
                                <i class="fas fa-calendar"></i> Age <span class="text-danger">*</span>
                            </label>
                            <input type="number"
                                   name="age"
                                   id="age"
                                   class="form-control @error('age') is-invalid @enderror"
                                   placeholder="Age"
                                   min="18"
                                   max="100"
                                   value="{{ old('age', $profile->age ?? '') }}"
                                   required>
                            @error('age')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Gender -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="gender" class="form-label">
                                <i class="fas fa-venus-mars"></i> Gender <span class="text-danger">*</span>
                            </label>
                            <select name="gender"
                                    id="gender"
                                    class="form-select @error('gender') is-invalid @enderror"
                                    required>
                                <option value="">Select Gender</option>
                                <option value="male" {{ old('gender', $profile->gender ?? '') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender', $profile->gender ?? '') == 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Phone -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="phone" class="form-label">
                                <i class="fas fa-phone"></i> Phone <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="phone"
                                   id="phone"
                                   class="form-control @error('phone') is-invalid @enderror"
                                   placeholder="+20 123 456 7890"
                                   value="{{ old('phone', $profile->phone ?? '') }}"
                                   required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Company -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="company" class="form-label">
                                <i class="fas fa-building"></i> Company <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="company"
                                   id="company"
                                   class="form-control @error('company') is-invalid @enderror"
                                   placeholder="Company name"
                                   value="{{ old('company', $profile->company ?? '') }}"
                                   required>
                            @error('company')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Job Title -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="job_title" class="form-label">
                                <i class="fas fa-briefcase"></i> Job Title <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="job_title"
                                   id="job_title"
                                   class="form-control @error('job_title') is-invalid @enderror"
                                   placeholder="Your job title"
                                   value="{{ old('job_title', $profile->job_title ?? '') }}"
                                   required>
                            @error('job_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Country with search -->
                <div class="form-group">
                    <label for="country-search" class="form-label">
                        <i class="fas fa-globe"></i> Country <span class="text-danger">*</span>
                    </label>
                    <div class="country-select-wrapper">
                        <input type="text"
                               id="country-search"
                               class="form-control mb-2"
                               placeholder="🔍 Search Country...">
                        <select name="country"
                                id="country-select"
                                class="form-select @error('country') is-invalid @enderror"
                                size="5"
                                required>
                            @php
                                $countries = [
                                    "Afghanistan", "Albania", "Algeria", "Andorra", "Angola", "Argentina", "Armenia",
                                    "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados",
                                    "Belarus", "Belgium", "Belize", "Benin", "Bhutan", "Bolivia", "Bosnia and Herzegovina",
                                    "Botswana", "Brazil", "Brunei", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia",
                                    "Cameroon", "Canada", "Cape Verde", "Central African Republic", "Chad", "Chile", "China",
                                    "Colombia", "Comoros", "Congo", "Costa Rica", "Croatia", "Cuba", "Cyprus", "Czech Republic",
                                    "Denmark", "Djibouti", "Dominica", "Dominican Republic", "East Timor", "Ecuador", "Egypt",
                                    "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Fiji", "Finland",
                                    "France", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Greece", "Grenada", "Guatemala",
                                    "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Honduras", "Hungary", "Iceland", "India",
                                    "Indonesia", "Iran", "Iraq", "Ireland", "Israel", "Italy", "Ivory Coast", "Jamaica", "Japan",
                                    "Jordan", "Kazakhstan", "Kenya", "Kiribati", "North Korea", "South Korea", "Kuwait",
                                    "Kyrgyzstan", "Laos", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libya", "Liechtenstein",
                                    "Lithuania", "Luxembourg", "Macedonia", "Madagascar", "Malawi", "Malaysia", "Maldives",
                                    "Mali", "Malta", "Marshall Islands", "Mauritania", "Mauritius", "Mexico", "Micronesia",
                                    "Moldova", "Monaco", "Mongolia", "Montenegro", "Morocco", "Mozambique", "Myanmar", "Namibia",
                                    "Nauru", "Nepal", "Netherlands", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Norway",
                                    "Oman", "Pakistan", "Palau", "Palestine", "Panama", "Papua New Guinea", "Paraguay", "Peru",
                                    "Philippines", "Poland", "Portugal", "Qatar", "Romania", "Russia", "Rwanda", "Saint Kitts and Nevis",
                                    "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe",
                                    "Saudi Arabia", "Senegal", "Serbia", "Seychelles", "Sierra Leone", "Singapore", "Slovakia",
                                    "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Sudan", "Spain", "Sri Lanka",
                                    "Sudan", "Suriname", "Swaziland", "Sweden", "Switzerland", "Syria", "Taiwan", "Tajikistan",
                                    "Tanzania", "Thailand", "Togo", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey",
                                    "Turkmenistan", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom",
                                    "United States", "Uruguay", "Uzbekistan", "Vanuatu", "Vatican City", "Venezuela", "Vietnam",
                                    "Yemen", "Zambia", "Zimbabwe"
                                ];
                                $selectedCountry = old('country', $profile->country ?? '');
                            @endphp
                            @foreach($countries as $country)
                                <option value="{{ $country }}" {{ $selectedCountry == $country ? 'selected' : '' }}>
                                    {{ $country }}
                                </option>
                            @endforeach
                        </select>
                        @error('country')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Address -->
                <div class="form-group">
                    <label for="address" class="form-label">
                        <i class="fas fa-map-marker-alt"></i> Address <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                           name="address"
                           id="address"
                           class="form-control @error('address') is-invalid @enderror"
                           placeholder="Street address, city"
                           value="{{ old('address', $profile->address ?? '') }}"
                           required>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Bio -->
                <div class="form-group">
                    <label for="bio" class="form-label">
                        <i class="fas fa-pen"></i> Bio <span class="text-danger">*</span>
                    </label>
                    <textarea name="bio"
                              id="bio"
                              class="form-control @error('bio') is-invalid @enderror"
                              rows="4"
                              placeholder="Write something about yourself, your experience, and skills..."
                              maxlength="500"
                              required>{{ old('bio', $profile->bio ?? '') }}</textarea>
                    <small class="text-muted">
                        <span id="bio-count">0</span>/500 characters
                    </small>
                    @error('bio')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Profile Photo -->
                <div class="form-group">
                    <label for="profile_photo" class="form-label">
                        <i class="fas fa-camera"></i> Profile Photo
                    </label>
                    <input type="file"
                           name="profile_photo"
                           id="profile_photo"
                           class="form-control @error('profile_photo') is-invalid @enderror"
                           accept="image/jpeg,image/png,image/jpg,image/gif,image/svg+xml">
                    <small class="text-muted">Accepted formats: JPG, PNG, GIF, SVG (Max: 2MB)</small>
                    @error('profile_photo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <img id="photo-preview" class="photo-preview" alt="Photo preview">
                </div>

                <!-- Submit Button -->
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <a href="{{ url('/') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> {{ isset($profile) ? 'Update Profile' : 'Save Profile' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Country search functionality
    const search = document.getElementById('country-search');
    const select = document.getElementById('country-select');

    search.addEventListener('input', function() {
        const filter = this.value.toLowerCase();
        for (let option of select.options) {
            const text = option.value.toLowerCase();
            option.style.display = text.includes(filter) ? '' : 'none';
        }
    });

    // Bio character counter
    const bioTextarea = document.getElementById('bio');
    const bioCount = document.getElementById('bio-count');

    function updateBioCount() {
        bioCount.textContent = bioTextarea.value.length;
    }

    bioTextarea.addEventListener('input', updateBioCount);
    updateBioCount(); // Initialize count

    // Photo preview
    const photoInput = document.getElementById('profile_photo');
    const photoPreview = document.getElementById('photo-preview');

    photoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Check file size (2MB = 2097152 bytes)
            if (file.size > 2097152) {
                alert('File size must be less than 2MB');
                photoInput.value = '';
                photoPreview.style.display = 'none';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                photoPreview.src = e.target.result;
                photoPreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            photoPreview.style.display = 'none';
        }
    });

    // Form validation
    (function() {
        'use strict';
        const forms = document.querySelectorAll('form');
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();
</script>
@endsection
