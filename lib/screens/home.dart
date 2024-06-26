import 'package:active_ecommerce_flutter/custom/CommonFunctoins.dart';
import 'package:active_ecommerce_flutter/helpers/addons_helper.dart';
import 'package:active_ecommerce_flutter/helpers/business_setting_helper.dart';
import 'package:active_ecommerce_flutter/my_theme.dart';
import 'package:active_ecommerce_flutter/providers/locale_provider.dart';
import 'package:active_ecommerce_flutter/screens/filter.dart';
import 'package:active_ecommerce_flutter/screens/flash_deal_list.dart';
import 'package:active_ecommerce_flutter/screens/product_details.dart';
import 'package:active_ecommerce_flutter/screens/todays_deal_products.dart';
import 'package:active_ecommerce_flutter/screens/top_selling_products.dart';
import 'package:active_ecommerce_flutter/screens/category_products.dart';
import 'package:active_ecommerce_flutter/screens/category_list.dart';
import 'package:active_ecommerce_flutter/ui_sections/drawer.dart';
import 'package:active_ecommerce_flutter/utils/ui.dart';
import 'package:active_ecommerce_flutter/widgets/global/BAMRatingWidget.dart';
import 'package:flutter/material.dart';
import 'package:carousel_slider/carousel_slider.dart';
import 'package:active_ecommerce_flutter/repositories/sliders_repository.dart';
import 'package:active_ecommerce_flutter/repositories/category_repository.dart';
import 'package:active_ecommerce_flutter/repositories/product_repository.dart';
import 'package:active_ecommerce_flutter/app_config.dart';
import 'package:provider/provider.dart';
import 'package:shimmer/shimmer.dart';
import 'package:active_ecommerce_flutter/custom/toast_component.dart';
import 'package:toast/toast.dart';
import 'package:active_ecommerce_flutter/ui_elements/product_card.dart';
import 'package:active_ecommerce_flutter/helpers/shimmer_helper.dart';
import 'package:active_ecommerce_flutter/helpers/shared_value_helper.dart';
import 'package:flutter_gen/gen_l10n/app_localizations.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';

class Home extends StatefulWidget {
  Home({Key key, this.title, this.show_back_button = false, go_back = true})
      : super(key: key);

  // This widget is the home page of your application. It is stateful, meaning
  // that it has a State object (defined below) that contains fields that affect
  // how it looks.

  // This class is the configuration for the state. It holds the values (in this
  // case the title) provided by the parent (in this case the App widget) and
  // used by the build method of the State. Fields in a Widget subclass are
  // always marked "final".

  final String title;
  bool show_back_button;
  bool go_back;

  @override
  _HomeState createState() => _HomeState();
}

class _HomeState extends State<Home> with TickerProviderStateMixin {
  final GlobalKey<ScaffoldState> _scaffoldKey = new GlobalKey<ScaffoldState>();
  int _current_slider = 0;
  ScrollController _featuredProductScrollController;
  ScrollController _mainScrollController = ScrollController();

  AnimationController pirated_logo_controller;
  Animation pirated_logo_animation;

  var _carouselImageList = [];
  var _featuredCategoryList = [];
  var _featuredProductList = [];
  var _topProductList = [];
  bool _isTopProductInitial = true;
  bool _isProductInitial = true;
  bool _isCategoryInitial = true;
  bool _isCarouselInitial = true;
  int _totalProductData = 0;
  int _productPage = 1;
  bool _showProductLoadingContainer = false;

  @override
  void initState() {
    print("app_mobile_language.en${app_mobile_language.$}");
    print("app_language.${app_language.$}");
    print("app_language_rtl${app_language_rtl.$}");

    // TODO: implement initState
    super.initState();
    // In initState()
    if (AppConfig.purchase_code == "") {
      initPiratedAnimation();
    }

    fetchAll();

    _mainScrollController.addListener(() {
      //print("position: " + _xcrollController.position.pixels.toString());
      //print("max: " + _xcrollController.position.maxScrollExtent.toString());

      if (_mainScrollController.position.pixels ==
          _mainScrollController.position.maxScrollExtent) {
        setState(() {
          _productPage++;
        });
        _showProductLoadingContainer = true;
        fetchFeaturedProducts();
      }
    });
  }

  fetchAll() {
    fetchCarouselImages();
    fetchFeaturedCategories();
    fetchFeaturedProducts();
    fetchTopSellingProducts();
    // AddonsHelper().setAddonsData();
    // BusinessSettingHelper().setBusinessSettingData();
  }

  fetchCarouselImages() async {
    var carouselResponse = await SlidersRepository().getSliders();
    carouselResponse.sliders.forEach((slider) {
      _carouselImageList.add(slider.photo);
    });
    _isCarouselInitial = false;
    setState(() {});
  }

  fetchFeaturedCategories() async {
    var categoryResponse = await CategoryRepository().getFeturedCategories();
    _featuredCategoryList.addAll(categoryResponse.categories);
    _isCategoryInitial = false;
    setState(() {});
  }

  //Fetch products here
  fetchTopSellingProducts() async {
    var productResponse = await ProductRepository().getBestSellingProducts();
    print("the top selling prodcuts array here");
    print(productResponse.products.first.toJson());
    _topProductList.addAll(productResponse.products);
    _isTopProductInitial = false;
    setState(() {});
  }

  fetchFeaturedProducts() async {
    var productResponse = await ProductRepository().getFeaturedProducts(
      page: _productPage,
    );

    _featuredProductList.addAll(productResponse.products);
    _isProductInitial = false;
    _totalProductData = productResponse.meta.total;
    _showProductLoadingContainer = false;
    setState(() {});
  }

  reset() {
    _carouselImageList.clear();
    _featuredCategoryList.clear();
    _isCarouselInitial = true;
    _isCategoryInitial = true;
    _isTopProductInitial = true;
    _topProductList.clear();
    setState(() {});

    resetProductList();
  }

  Future<void> _onRefresh() async {
    reset();
    fetchAll();
  }

  resetProductList() {
    _featuredProductList.clear();
    _isProductInitial = true;
    _totalProductData = 0;
    _productPage = 1;
    _showProductLoadingContainer = false;
    setState(() {});
  }

  initPiratedAnimation() {
    pirated_logo_controller = AnimationController(
        vsync: this, duration: Duration(milliseconds: 2000));
    pirated_logo_animation = Tween(begin: 40.0, end: 60.0).animate(
        CurvedAnimation(
            curve: Curves.bounceOut, parent: pirated_logo_controller));

    pirated_logo_controller.addStatusListener((AnimationStatus status) {
      if (status == AnimationStatus.completed) {
        pirated_logo_controller.repeat();
      }
    });

    pirated_logo_controller.forward();
  }

  @override
  void dispose() {
    // TODO: implement dispose
    super.dispose();
    pirated_logo_controller?.dispose();
    _mainScrollController.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final double statusBarHeight = MediaQuery.of(context).padding.top;
    //print(MediaQuery.of(context).viewPadding.top);

    return WillPopScope(
      onWillPop: () async {
        CommonFunctions(context).appExitDialog();
        return widget.go_back;
      },
      child: Directionality(
        textDirection:
            app_language_rtl.$ ? TextDirection.rtl : TextDirection.ltr,
        child: Scaffold(
            key: _scaffoldKey,
            backgroundColor: Colors.white,
            appBar: buildAppBar(statusBarHeight, context),
            drawer: MainDrawer(),
            body: Stack(
              children: [
                RefreshIndicator(
                  color: MyTheme.accent_color,
                  backgroundColor: Colors.white,
                  onRefresh: _onRefresh,
                  displacement: 0,
                  child: CustomScrollView(
                    controller: _mainScrollController,
                    physics: const BouncingScrollPhysics(
                        parent: AlwaysScrollableScrollPhysics()),
                    slivers: <Widget>[
                      SliverList(
                        delegate: SliverChildListDelegate([
                          AppConfig.purchase_code == ""
                              ? Padding(
                                  padding: const EdgeInsets.fromLTRB(
                                    8.0,
                                    16.0,
                                    8.0,
                                    0.0,
                                  ),
                                  child: Container(
                                    height: 140,
                                    color: Colors.black,
                                    child: Stack(
                                      children: [
                                        Positioned(
                                            left: 20,
                                            top: 0,
                                            child: AnimatedBuilder(
                                                animation:
                                                    pirated_logo_animation,
                                                builder: (context, child) {
                                                  return Image.asset(
                                                    "assets/pirated_square.png",
                                                    height:
                                                        pirated_logo_animation
                                                            .value,
                                                    color: Colors.white,
                                                  );
                                                })),
                                        Center(
                                          child: Padding(
                                            padding: const EdgeInsets.only(
                                                top: 24.0, left: 24, right: 24),
                                            child: Text(
                                              "This is a pirated app. Do not use this. It may have security issues.",
                                              style: TextStyle(
                                                  color: Colors.white,
                                                  fontSize: 18),
                                            ),
                                          ),
                                        ),
                                      ],
                                    ),
                                  ),
                                )
                              : Container(),
                          Padding(
                            padding: const EdgeInsets.fromLTRB(
                              8.0,
                              16.0,
                              8.0,
                              0.0,
                            ),
                            child: buildHomeCarouselSlider(context),
                          ),
                          Padding(
                            padding: const EdgeInsets.fromLTRB(
                              8.0,
                              16.0,
                              8.0,
                              0.0,
                            ),
                            child: buildHomeMenuRow(context),
                          ),
                        ]),
                      ),
                      SliverList(
                        delegate: SliverChildListDelegate([
                          Padding(
                            padding: const EdgeInsets.fromLTRB(
                              16.0,
                              16.0,
                              16.0,
                              0.0,
                            ),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Row(
                                  mainAxisAlignment:
                                      MainAxisAlignment.spaceBetween,
                                  children: [
                                    Text(
                                      //TODO: change to app localization
                                      "Trending",
                                      style: TextStyle(
                                          fontSize: 24.sp,
                                          fontWeight: FontWeight.w700),
                                    ),
                                    Icon(Icons.chevron_right, size: 30.sp)
                                  ],
                                ),
                              ],
                            ),
                          ),
                        ]),
                      ),
                      SliverToBoxAdapter(
                        child: Padding(
                          padding: const EdgeInsets.fromLTRB(
                            16.0,
                            16.0,
                            0.0,
                            0.0,
                          ),
                          child: SizedBox(
                            height: 220,
                            child: buildTopSellingProducts(context),
                          ),
                        ),
                      ),
                      SliverList(
                        delegate: SliverChildListDelegate([
                          Padding(
                            padding: const EdgeInsets.fromLTRB(
                              16.0,
                              16.0,
                              16.0,
                              0.0,
                            ),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Row(
                                  mainAxisAlignment:
                                      MainAxisAlignment.spaceBetween,
                                  children: [
                                    Text(
                                      //TODO: change to app localization
                                      "New Goods",
                                      style: TextStyle(
                                          fontSize: 24.sp,
                                          fontWeight: FontWeight.w700),
                                    ),
                                    Icon(Icons.chevron_right, size: 30.sp)
                                  ],
                                ),
                              ],
                            ),
                          ),
                          SingleChildScrollView(
                            child: Column(
                              children: [
                                Padding(
                                  padding: const EdgeInsets.fromLTRB(
                                    16.0,
                                    16.0,
                                    8.0,
                                    0.0,
                                  ),
                                  child: SizedBox(
                                      height: 220,
                                      child:
                                          buildHomeFeaturedProducts(context)),
                                ),
                              ],
                            ),
                          ),
                          Container(
                            height: 80,
                          )
                        ]),
                      ),
                    ],
                  ),
                ),
                Align(
                    alignment: Alignment.center,
                    child: buildProductLoadingContainer())
              ],
            )),
      ),
    );
  }

  buildHomeFeaturedProducts(context) {
    if (_isProductInitial && _featuredProductList.length == 0) {
      return SingleChildScrollView(
          child: ShimmerHelper().buildProductGridShimmer(
              scontroller: _featuredProductScrollController));
    } else if (_featuredProductList.length > 0) {
      //snapshot.hasData

      return ListView.builder(
          scrollDirection: Axis.horizontal,
          itemCount: _featuredProductList.length,
          itemBuilder: (context, index) {
            return Container(
              width: 128.w,
              margin: EdgeInsets.only(right: 14.0.w),
              child: GestureDetector(
                onTap: () {
                  Navigator.push(context, MaterialPageRoute(builder: (context) {
                    return ProductDetails(
                      id: _featuredProductList[index].id,
                    );
                  }));
                },
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.start,
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: <Widget>[
                    ClipRRect(
                        borderRadius: BorderRadius.vertical(
                            top: Radius.circular(20.r),
                            bottom: Radius.circular(20.r)),
                        child: FadeInImage.assetNetwork(
                          placeholder: 'assets/placeholder.png',
                          image: _featuredProductList[index].thumbnail_image,
                          height: 159.47.h,
                          width: double.maxFinite,
                          fit: BoxFit.fill,
                        )),
                    addVerticalSpace(6.0.h),
                    Center(
                        child: BAMRatingWidget(
                      rating: _featuredProductList[index].rating.toString(),
                    )),
                    addVerticalSpace(6.0.h),
                    Text(
                      _featuredProductList[index].name,
                      textAlign: TextAlign.start,
                      overflow: TextOverflow.ellipsis,
                      maxLines: 2,
                      style: TextStyle(
                        fontSize: 14.0.sp,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                    addVerticalSpace(6.0.h),
                    Text(
                      _featuredProductList[index].main_price,
                      // textAlign: TextAlign.left,
                      // overflow: TextOverflow.ellipsis,
                      // maxLines: 1,
                      style: TextStyle(
                        color: MyTheme.accent_color,
                        fontSize: 13.0.sp,
                        fontWeight: FontWeight.w500,
                      ),
                    )
                  ],
                ),
              ),
            );
          });
    } else if (_totalProductData == 0) {
      return Center(
          child: Text(
              AppLocalizations.of(context).common_no_product_is_available));
    } else {
      return Container(); // should never be happening
    }
  }

  // buildHomeFeaturedCategories(context) {
  //   if (_isCategoryInitial && _featuredCategoryList.length == 0) {
  //     return Row(
  //       children: [
  //         Padding(
  //             padding: const EdgeInsets.only(right: 8.0),
  //             child: ShimmerHelper().buildBasicShimmer(
  //                 height: 120.0,
  //                 width: (MediaQuery.of(context).size.width - 32) / 3)),
  //         Padding(
  //             padding: const EdgeInsets.only(right: 8.0),
  //             child: ShimmerHelper().buildBasicShimmer(
  //                 height: 120.0,
  //                 width: (MediaQuery.of(context).size.width - 32) / 3)),
  //         Padding(
  //             padding: const EdgeInsets.only(right: 0.0),
  //             child: ShimmerHelper().buildBasicShimmer(
  //                 height: 120.0,
  //                 width: (MediaQuery.of(context).size.width - 32) / 3)),
  //       ],
  //     );
  //   } else if (_featuredCategoryList.length > 0) {
  //     //snapshot.hasData
  //     return ListView.builder(
  //         scrollDirection: Axis.horizontal,
  //         itemCount: _featuredCategoryList.length,
  //         itemExtent: 120,
  //         itemBuilder: (context, index) {
  //           return Padding(
  //             padding: const EdgeInsets.symmetric(horizontal: 3),
  //             child: GestureDetector(
  //               onTap: () {
  //                 Navigator.push(context, MaterialPageRoute(builder: (context) {
  //                   return CategoryProducts(
  //                     category_id: _featuredCategoryList[index].id,
  //                     category_name: _featuredCategoryList[index].name,
  //                   );
  //                 }));
  //               },
  //               child: Card(
  //                 clipBehavior: Clip.antiAliasWithSaveLayer,
  //                 shape: RoundedRectangleBorder(
  //                   side: new BorderSide(color: MyTheme.light_grey, width: 1.0),
  //                   borderRadius: BorderRadius.circular(16.0),
  //                 ),
  //                 elevation: 0.0,
  //                 child: Column(
  //                   mainAxisAlignment: MainAxisAlignment.center,
  //                   children: <Widget>[
  //                     Container(
  //                         //width: 100,
  //                         height: 100,
  //                         child: ClipRRect(
  //                             borderRadius: BorderRadius.vertical(
  //                                 top: Radius.circular(16),
  //                                 bottom: Radius.zero),
  //                             child: FadeInImage.assetNetwork(
  //                               placeholder: 'assets/placeholder.png',
  //                               image:
  //                                   _featuredCategoryList[index].banner,
  //                               fit: BoxFit.cover,
  //                             ))),
  //                     Padding(
  //                       padding: EdgeInsets.fromLTRB(8, 8, 8, 4),
  //                       child: Container(
  //                         height: 32,
  //                         child: Text(
  //                           _featuredCategoryList[index].name,
  //                           textAlign: TextAlign.center,
  //                           overflow: TextOverflow.ellipsis,
  //                           maxLines: 2,
  //                           style: TextStyle(
  //                               fontSize: 11, color: MyTheme.font_grey),
  //                         ),
  //                       ),
  //                     ),
  //                   ],
  //                 ),
  //               ),
  //             ),
  //           );
  //         });
  //   } else if (!_isCategoryInitial && _featuredCategoryList.length == 0) {
  //     return Container(
  //         height: 100,
  //         child: Center(
  //             child: Text(
  //           AppLocalizations.of(context).home_screen_no_category_found,
  //           style: TextStyle(color: MyTheme.font_grey),
  //         )));
  //   } else {
  //     // should not be happening
  //     return Container(
  //       height: 100,
  //     );
  //   }
  // }

  buildTopSellingProducts(context) {
    if (_isTopProductInitial && _topProductList.length == 0) {
      return Row(
        children: [
          Padding(
              padding: const EdgeInsets.only(right: 8.0),
              child: ShimmerHelper().buildBasicShimmer(
                  height: 120.0,
                  width: (MediaQuery.of(context).size.width - 32) / 3)),
          Padding(
              padding: const EdgeInsets.only(right: 8.0),
              child: ShimmerHelper().buildBasicShimmer(
                  height: 120.0,
                  width: (MediaQuery.of(context).size.width - 32) / 3)),
          Padding(
              padding: const EdgeInsets.only(right: 0.0),
              child: ShimmerHelper().buildBasicShimmer(
                  height: 120.0,
                  width: (MediaQuery.of(context).size.width - 32) / 3)),
        ],
      );
    } else if (_topProductList.length > 0) {
      //snapshot.hasData
      return ListView.builder(
          scrollDirection: Axis.horizontal,
          itemCount: _topProductList.length,
          itemBuilder: (context, index) {
            return Container(
              width: 128.w,
              margin: EdgeInsets.only(right: 14.0.w),
              child: GestureDetector(
                onTap: () {
                  Navigator.push(context, MaterialPageRoute(builder: (context) {
                    return ProductDetails(
                      id: _topProductList[index].id,
                    );
                  }));
                },
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.start,
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: <Widget>[
                    ClipRRect(
                        borderRadius: BorderRadius.vertical(
                            top: Radius.circular(20.r),
                            bottom: Radius.circular(20.r)),
                        child: FadeInImage.assetNetwork(
                          placeholder: 'assets/placeholder.png',
                          image: _topProductList[index].thumbnail_image,
                          height: 159.47.h,
                          width: double.maxFinite,
                          fit: BoxFit.fill,
                        )),
                    addVerticalSpace(6.0.h),
                    Center(
                        child: BAMRatingWidget(
                      rating: _topProductList[index].rating.toString(),
                    )),
                    addVerticalSpace(6.0.h),
                    Text(
                      _topProductList[index].name,
                      textAlign: TextAlign.start,
                      overflow: TextOverflow.ellipsis,
                      maxLines: 2,
                      style: TextStyle(
                        fontSize: 14.0.sp,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                    addVerticalSpace(6.0.h),
                    Text(
                      _topProductList[index].main_price,
                      // textAlign: TextAlign.left,
                      // overflow: TextOverflow.ellipsis,
                      // maxLines: 1,
                      style: TextStyle(
                        color: MyTheme.accent_color,
                        fontSize: 13.0.sp,
                        fontWeight: FontWeight.w500,
                      ),
                    )
                  ],
                ),
              ),
            );
          });
    } else if (!_isTopProductInitial && _topProductList.length == 0) {
      return Container(
          height: 100,
          child: Center(
              child: Text(
            AppLocalizations.of(context).common_no_product_is_available,
            style: TextStyle(color: MyTheme.font_grey),
          )));
    } else {
      // should not be happening
      return Container(
        height: 100,
      );
    }
  }

  buildHomeMenuRow(BuildContext context) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      crossAxisAlignment: CrossAxisAlignment.center,
      children: [
        // GestureDetector(
        //   onTap: () {
        //     Navigator.push(context, MaterialPageRoute(builder: (context) {
        //       return CategoryList(
        //         is_top_category: true,
        //       );
        //     }));
        //   },
        //   child: Container(
        //     height: 100,
        //     width: MediaQuery.of(context).size.width / 5 - 4,
        //     child: Column(
        //       children: [
        //         Container(
        //             height: 57,
        //             width: 57,
        //             decoration: BoxDecoration(
        //                 shape: BoxShape.circle,
        //                 border:
        //                     Border.all(color: MyTheme.light_grey, width: 1)),
        //             child: Padding(
        //               padding: const EdgeInsets.all(16.0),
        //               child: Image.asset("assets/top_categories.png"),
        //             )),
        //         Padding(
        //           padding: const EdgeInsets.only(top: 8),
        //           child: Text(
        //             AppLocalizations.of(context).home_screen_top_categories,
        //             textAlign: TextAlign.center,
        //             style: TextStyle(
        //                 color: Color.fromRGBO(132, 132, 132, 1),
        //                 fontWeight: FontWeight.w300),
        //           ),
        //         )
        //       ],
        //     ),
        //   ),
        // ),
        GestureDetector(
          onTap: () {
            Navigator.push(context, MaterialPageRoute(builder: (context) {
              return Filter(
                selected_filter: "brands",
              );
            }));
          },
          child: Container(
            height: 100,
            width: MediaQuery.of(context).size.width / 5 - 4,
            child: Column(
              children: [
                Container(
                    height: 57,
                    width: 57,
                    decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        border:
                            Border.all(color: MyTheme.light_grey, width: 1)),
                    child: Padding(
                      padding: const EdgeInsets.all(16.0),
                      child: Image.asset("assets/brands.png"),
                    )),
                Padding(
                    padding: const EdgeInsets.only(top: 8),
                    child: Text(AppLocalizations.of(context).home_screen_brands,
                        textAlign: TextAlign.center,
                        style: TextStyle(
                            color: Color.fromRGBO(132, 132, 132, 1),
                            fontWeight: FontWeight.w300))),
              ],
            ),
          ),
        ),
        GestureDetector(
          onTap: () {
            Navigator.push(context, MaterialPageRoute(builder: (context) {
              return TopSellingProducts();
            }));
          },
          child: Container(
            height: 100,
            width: MediaQuery.of(context).size.width / 5 - 4,
            child: Column(
              children: [
                Container(
                    height: 57,
                    width: 57,
                    decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        border:
                            Border.all(color: MyTheme.light_grey, width: 1)),
                    child: Padding(
                      padding: const EdgeInsets.all(16.0),
                      child: Image.asset("assets/top_sellers.png"),
                    )),
                Padding(
                    padding: const EdgeInsets.only(top: 8),
                    child: Text(
                        AppLocalizations.of(context).home_screen_top_sellers,
                        textAlign: TextAlign.center,
                        style: TextStyle(
                            color: Color.fromRGBO(132, 132, 132, 1),
                            fontWeight: FontWeight.w300))),
              ],
            ),
          ),
        ),
        GestureDetector(
          onTap: () {
            Navigator.push(context, MaterialPageRoute(builder: (context) {
              return TodaysDealProducts();
            }));
          },
          child: Container(
            height: 100,
            width: MediaQuery.of(context).size.width / 5 - 4,
            child: Column(
              children: [
                Container(
                    height: 57,
                    width: 57,
                    decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        border:
                            Border.all(color: MyTheme.light_grey, width: 1)),
                    child: Padding(
                      padding: const EdgeInsets.all(16.0),
                      child: Image.asset("assets/todays_deal.png"),
                    )),
                Padding(
                    padding: const EdgeInsets.only(top: 8),
                    child: Text(
                        AppLocalizations.of(context).home_screen_todays_deal,
                        textAlign: TextAlign.center,
                        style: TextStyle(
                            color: Color.fromRGBO(132, 132, 132, 1),
                            fontWeight: FontWeight.w300))),
              ],
            ),
          ),
        ),
        GestureDetector(
          onTap: () {
            Navigator.push(context, MaterialPageRoute(builder: (context) {
              return FlashDealList();
            }));
          },
          child: Container(
            height: 100,
            width: MediaQuery.of(context).size.width / 5 - 4,
            child: Column(
              children: [
                Container(
                    height: 57,
                    width: 57,
                    decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        border:
                            Border.all(color: MyTheme.light_grey, width: 1)),
                    child: Padding(
                      padding: const EdgeInsets.all(16.0),
                      child: Image.asset("assets/flash_deal.png"),
                    )),
                Padding(
                    padding: const EdgeInsets.only(top: 8),
                    child: Text(
                        AppLocalizations.of(context).home_screen_flash_deal,
                        textAlign: TextAlign.center,
                        style: TextStyle(
                            color: Color.fromRGBO(132, 132, 132, 1),
                            fontWeight: FontWeight.w300))),
              ],
            ),
          ),
        )
      ],
    );
  }

  buildHomeCarouselSlider(context) {
    if (_isCarouselInitial && _carouselImageList.length == 0) {
      return Padding(
        padding: const EdgeInsets.only(left: 5.0, right: 5.0),
        child: Shimmer.fromColors(
          baseColor: MyTheme.shimmer_base,
          highlightColor: MyTheme.shimmer_highlighted,
          child: Container(
            height: 135,
            width: double.infinity,
            color: Colors.white,
          ),
        ),
      );
    } else if (_carouselImageList.length > 0) {
      return CarouselSlider(
        options: CarouselOptions(
            aspectRatio: 2.67,
            viewportFraction: 1,
            initialPage: 0,
            enableInfiniteScroll: true,
            reverse: false,
            autoPlay: true,
            autoPlayInterval: Duration(seconds: 5),
            autoPlayAnimationDuration: Duration(milliseconds: 1000),
            autoPlayCurve: Curves.easeInCubic,
            enlargeCenterPage: true,
            scrollDirection: Axis.horizontal,
            onPageChanged: (index, reason) {
              setState(() {
                _current_slider = index;
              });
            }),
        items: _carouselImageList.map((i) {
          return Builder(
            builder: (BuildContext context) {
              return Stack(
                children: <Widget>[
                  Container(
                      width: double.infinity,
                      margin: EdgeInsets.symmetric(horizontal: 5.0),
                      child: ClipRRect(
                          borderRadius: BorderRadius.all(Radius.circular(30.r)),
                          child: FadeInImage.assetNetwork(
                            placeholder: 'assets/placeholder_rectangle.png',
                            image: i,
                            fit: BoxFit.fill,
                          ))),
                  Align(
                    alignment: Alignment.bottomCenter,
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: _carouselImageList.map((url) {
                        int index = _carouselImageList.indexOf(url);
                        return Container(
                          width: 7.0,
                          height: 7.0,
                          margin: EdgeInsets.symmetric(
                              vertical: 10.0, horizontal: 4.0),
                          decoration: BoxDecoration(
                            shape: BoxShape.circle,
                            color: _current_slider == index
                                ? MyTheme.white
                                : Color.fromRGBO(112, 112, 112, .3),
                          ),
                        );
                      }).toList(),
                    ),
                  ),
                ],
              );
            },
          );
        }).toList(),
      );
    } else if (!_isCarouselInitial && _carouselImageList.length == 0) {
      return Container(
          height: 100,
          child: Center(
              child: Text(
            AppLocalizations.of(context).home_screen_no_carousel_image_found,
            style: TextStyle(color: MyTheme.font_grey),
          )));
    } else {
      // should not be happening
      return Container(
        height: 100,
      );
    }
  }

  AppBar buildAppBar(double statusBarHeight, BuildContext context) {
    return AppBar(
      centerTitle: true,
      backgroundColor: Colors.white,
      leading: GestureDetector(
        onTap: () {
          _scaffoldKey.currentState.openDrawer();
        },
        child: widget.show_back_button
            ? Builder(
                builder: (context) => IconButton(
                    icon: Icon(Icons.arrow_back, color: MyTheme.dark_grey),
                    onPressed: () {
                      if (!widget.go_back) {
                        return;
                      }
                      return Navigator.of(context).pop();
                    }),
              )
            : Builder(
                builder: (context) => Padding(
                  padding: const EdgeInsets.symmetric(
                      vertical: 18.0, horizontal: 0.0),
                  child: Container(
                    child: Image.asset(
                      'assets/hamburger.png',
                      height: 16,
                      //color: MyTheme.dark_grey,
                      color: MyTheme.dark_grey,
                    ),
                  ),
                ),
              ),
      ),
      title: Container(
        // height: kToolbarHeight +
        //     statusBarHeight -
        //     (MediaQuery.of(context).viewPadding.top > 40 ? 16.0 : 16.0),
        //MediaQuery.of(context).viewPadding.top is the statusbar height, with a notch phone it results almost 50, without a notch it shows 24.0.For safety we have checked if its greater than thirty
        // child: Container(
        //   child: Padding(
        //       padding: app_language_rtl.$
        //           ? const EdgeInsets.only(top: 14.0, bottom: 14, left: 12)
        //           : const EdgeInsets.only(top: 14.0, bottom: 14, right: 12),
        //       // when notification bell will be shown , the right padding will cease to exist.
        //       child: GestureDetector(
        //           onTap: () {
        //             Navigator.push(context,
        //                 MaterialPageRoute(builder: (context) {
        //               return Filter();
        //             }));
        //           },
        //           child: buildHomeSearchBox(context))),
        // ),
        child: Text(
          "Hello ${user_name.$}",
          style: TextStyle(
              fontSize: 24.0, fontWeight: FontWeight.w600, color: Colors.black),
        ),
      ),
      elevation: 0.0,
      titleSpacing: 0,
      actions: <Widget>[
        IconButton(
          onPressed: () {
            Navigator.push(context, MaterialPageRoute(builder: (context) {
              return Filter();
            }));
          },
          icon: Icon(Icons.search, color: Colors.black, size: 25),
        ),
        // IconButton(
        //     onPressed: () {},
        //     icon: Icon(Icons.shopping_cart_outlined,
        //         color: Colors.black, size: 25))
        //TODO: Uncomment out this line below for notifications
        // InkWell(
        //   onTap: () {
        //     ToastComponent.showDialog(
        //         AppLocalizations.of(context).common_coming_soon, context,
        //         gravity: Toast.CENTER, duration: Toast.LENGTH_LONG);
        //   },
        //   child: Visibility(
        //     visible: false,
        //     child: Padding(
        //       padding:
        //           const EdgeInsets.symmetric(vertical: 18.0, horizontal: 12.0),
        //       child: Image.asset(
        //         'assets/bell.png',
        //         height: 16,
        //         color: MyTheme.dark_grey,
        //       ),
        //     ),
        //   ),
        // ),
      ],
    );
  }

  buildHomeSearchBox(BuildContext context) {
    return TextField(
      onTap: () {
        Navigator.push(context, MaterialPageRoute(builder: (context) {
          return Filter();
        }));
      },
      autofocus: false,
      decoration: InputDecoration(
          hintText: AppLocalizations.of(context).home_screen_search,
          hintStyle: TextStyle(fontSize: 12.0, color: MyTheme.textfield_grey),
          enabledBorder: OutlineInputBorder(
            borderSide: BorderSide(color: MyTheme.textfield_grey, width: 0.5),
            borderRadius: const BorderRadius.all(
              const Radius.circular(16.0),
            ),
          ),
          focusedBorder: OutlineInputBorder(
            borderSide: BorderSide(color: MyTheme.textfield_grey, width: 1.0),
            borderRadius: const BorderRadius.all(
              const Radius.circular(16.0),
            ),
          ),
          prefixIcon: Padding(
            padding: const EdgeInsets.all(8.0),
            child: Icon(
              Icons.search,
              color: MyTheme.textfield_grey,
              size: 20,
            ),
          ),
          contentPadding: EdgeInsets.all(0.0)),
    );
  }

  Container buildProductLoadingContainer() {
    return Container(
      height: _showProductLoadingContainer ? 36 : 0,
      width: double.infinity,
      color: Colors.white,
      child: Center(
        child: Text(_totalProductData == _featuredProductList.length
            ? AppLocalizations.of(context).common_no_more_products
            : AppLocalizations.of(context).common_loading_more_products),
      ),
    );
  }
}
