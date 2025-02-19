@extends('layouts.app')

@section('title')
    Store Cart Page
@endsection

@section('content')
    <!-- Page Content -->
    <div class="page-content page-cart">
      <section
        class="store-breadcrumbs"
        data-aos="fade-down"
        data-aos-delay="100"
      >
        <div class="container">
          <div class="row">
            <div class="col-12">
              <nav aria-label="Close">
                <ol class="breadcrumb">
                  <li class="breadcrumb-item">
                    <a href="{{ route('home') }}">Home</a>
                  </li>
                  <li class="breadcrumb-item active">Cart</li>
                </ol>
              </nav>
            </div>
          </div>
        </div>
      </section>
    </div>

    <section class="store-cart">
      <div class="container">
        <div class="row" data-aos="fade-up" data-aos-delay="100">
          <div class="col-12 table-responsive">
            <table class="table table-borderless table-cart" aria-label="Close">
              <th>
                <tr>
                  <td>Image</td>
                  <td>Name &amp; Seller</td>
                  <td>Price</td>
                  <td>Menu</td>
                </tr>
              </th>
              <tbody>
                @php $totalPrice = 0 @endphp
                @foreach($carts as $cart)
                <tr>
                  <td style="width: 20%">
                    @if ($cart->product->galleries)
                    <img
                      src="{{ Storage::url($cart->product->galleries->first()->photo) }}"
                      alt=""
                      class="cart-image"
                    />
                    @endif
                  </td>
                  <td style="width: 35%">
                    <div class="product-title">{{ $cart->product->name }}</div>
                    <div class="product-subtitle">by {{ $cart->product->user->store_name }}</div>
                  </td>
                  <td style="width: 35%">
                    <div class="product-title">{{ number_format($cart->product->price) }}</div>
                    <div class="product-subtitle">Rp</div>
                  </td>
                  <td style="width: 20%">
                    <form action="{{ route('cart-delete', $cart->id) }}" method="POST">
                        @method('DELETE')
                        @csrf
                        <button type="submit" class="btn btn-remove-cart">Remove</button>
                    </form>
                  </td>
                </tr>
                @php $totalPrice += $cart->product->price @endphp
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
        <div class="row" data-aos="fade-up" data-aos-delay="150">
          <div class="col-12">
            <hr />
          </div>
          <div class="col-12">
            <div class="mb-4">Shipping Details</div>
          </div>
        </div>
        <form action="{{ route('checkout') }}" id="locations" enctype="multipart/form-data" method="POST">
            @csrf
            <input type="hidden" name="total_price" value="{{ $totalPrice }}">
            <div class="row mb-2" data-aos="fade-up" data-aos-delay="200">
                <div class="col-md-6">
                    <div class="form-group">
                    <label for="address_one">Address 1</label>
                    <input
                        type="text"
                        class="form-control"
                        id="address_one"
                        name="address_one"
                        value="{{ old('address_one') }}"
                    />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                    <label for="address_two">Address 2</label>
                    <input
                        type="text"
                        class="form-control"
                        id="address_two"
                        name="address_two"
                        value="{{ old('address_two') }}"
                    />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                    <label for="province_id">Province</label>
                    <select name="province_id" id="province_id" class="form-control" v-if="provinces" v-model="province_id">
                        <option v-for="province in provinces" :value="province.id">@{{ province.name }}</option>
                    </select>
                    <select v-else class="form-control">Pilih Salah Satu</select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                    <label for="regency_id">City</label>
                    <select name="regency_id" id="regency_id" class="form-control" v-if="regencies" v-model="regency_id">
                        <option v-for="regency in regencies" :value="regency.id">@{{ regency.name }}</option>
                    </select>
                    <select v-else class="form-control">Pilih Salah Satu</select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                    <label for="zip_code">Postal Code</label>
                    <input
                        type="text"
                        class="form-control"
                        id="zip_code"
                        name="zip_code"
                        value="{{ old('zip_code') }}"
                    />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                    <label for="country">Country</label>
                    <input
                        type="text"
                        class="form-control"
                        id="country"
                        name="country"
                        value="{{ old('country') }}"
                    />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                    <label for="phone_number">Mobile</label>
                    <input
                        type="text"
                        class="form-control"
                        id="phone_number"
                        name="phone_number"
                        value="{{ old('phone_number') }}"
                    />
                    </div>
                </div>
            </div>
            <div class="row" data-aos="fade-up" data-aos-delay="150">
            <div class="col-12">
                <hr />
            </div>
            <div class="col-12">
                <div class="mb-1">Payment Information</div>
            </div>
            </div>
            <div class="row" data-aos="fade-up" data-aos-delay="200">
            <div class="col-4 col-md-2">
                <div class="product-title">$0</div>
                <div class="product-subtitle">Country Tax</div>
            </div>
            <div class="col-4 col-md-3">
                <div class="product-title">$0</div>
                <div class="product-subtitle">Product Insurance</div>
            </div>
            <div class="col-4 col-md-2">
                <div class="product-title">$0</div>
                <div class="product-subtitle">Ship to Jakarta</div>
            </div>
            <div class="col-4 col-md-2">
                <div class="product-title text-success">{{ number_format($totalPrice ?? 0) }}</div>
                <div class="product-subtitle">Total</div>
            </div>
            <div class="col-8 col-md-3">
                <button type="submit" class="btn btn-success mt-4 px-4 btn-block">
                    Checkout Now
                </button>
            </div>
            </div>
        </form>
      </div>
    </section>
@endsection

@push('addon-script')
    <script src="/vendor/vue/vue.js"></script>
    <script src="https://unpkg.com/axios@1.6.7/dist/axios.min.js"></script>
    <script>
      var locations = new Vue({
        el: "#locations",
        mounted() {
          AOS.init();
          this.getProvinceData();
        },
        data: {
            provinces: null,
            regencies: null,
            province_id: null,
            regency_id: null
        },
        methods: {
            getProvinceData() {
                var self = this;
                axios.get('{{ route('api-provinces') }}')
                .then(function(response) {
                    self.provinces = response.data;
                })
            },
            getRegenciesData() {
                var self = this;
                axios.get('{{ url('api/regencies') }}/' + self.province_id)
                .then(function(response) {
                    self.regencies = response.data;
                })
            }
        },
        watch: {
            province_id: function(val, oldVal) {
                this.regency_id = null;
                this.getRegenciesData();
            }
        }
      });
    </script>
@endpush
