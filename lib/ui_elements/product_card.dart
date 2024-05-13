import 'package:active_ecommerce_flutter/my_theme.dart';
import 'package:active_ecommerce_flutter/utils/ui.dart';
import 'package:active_ecommerce_flutter/widgets/global/BAMRatingWidget.dart';
import 'package:flutter/material.dart';
import 'package:active_ecommerce_flutter/screens/product_details.dart';
import 'package:active_ecommerce_flutter/app_config.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:google_fonts/google_fonts.dart';
class ProductCard extends StatefulWidget {
  int id;
  String image;
  String name;
  String main_price;
  String stroked_price;
  bool has_discount;
  int rating;
  TextAlign titleAlign;

  ProductCard(
      {Key key,
      this.id,
      this.image,
      this.name,
      this.main_price,
      this.stroked_price,
      this.has_discount, this.rating, this.titleAlign = TextAlign.center })
      : super(key: key);

  @override
  _ProductCardState createState() => _ProductCardState();
}

class _ProductCardState extends State<ProductCard> {

  @override
  Widget build(BuildContext context) {
    print(widget.rating);

    print('The image is jhahaas ${widget.image}');
    print((MediaQuery.of(context).size.width - 48) / 2);
    return InkWell(
      onTap: () {
        Navigator.push(context, MaterialPageRoute(builder: (context) {
          return ProductDetails(
            id: widget.id,
          );
        }));
      },
      // child: Card(
      //    //clipBehavior: Clip.antiAliasWithSaveLayer,
      //   shape: RoundedRectangleBorder(
      //     side: new BorderSide(color: MyTheme.light_grey, width: 1.0),
      //     borderRadius: BorderRadius.circular(16.0),
      //   ),
      //   elevation: 0.0,
      //   child: Column(
      //       mainAxisAlignment: MainAxisAlignmenwt.center,
      //       crossAxisAlignment: CrossAxisAlignment.start,
      //       children: <Widget>[
      //         Container(
      //             width: double.infinity,
      //             //height: 158,
      //             height: (( MediaQuery.of(context).size.width - 28 ) / 2) + 2,
      //             child: ClipRRect(
      //               clipBehavior: Clip.hardEdge,
      //                 borderRadius: BorderRadius.vertical(
      //                     top: Radius.circular(16), bottom: Radius.zero),
      //                 child: FadeInImage.assetNetwork(
      //                   placeholder: 'assets/placeholder.png',
      //                   image:  widget.image,
      //                   fit: BoxFit.cover,
      //                 ))),
      //         Container(
      //           height: 90,
      //           child: Column(
      //             crossAxisAlignment: CrossAxisAlignment.start,
      //             children: [
      //               Padding(
      //                 padding: EdgeInsets.fromLTRB(16, 8, 16, 0),
      //                 child: Text(
      //                   widget.name,
      //                   overflow: TextOverflow.ellipsis,
      //                   maxLines: 2,
      //                   style: TextStyle(
      //                       color: MyTheme.font_grey,
      //                       fontSize: 14,
      //                       height: 1.2,
      //                       fontWeight: FontWeight.w400),
      //                 ),
      //               ),
      //               Padding(
      //                 padding: EdgeInsets.fromLTRB(16, 4, 16, 0),
      //                 child: Text(
      //                   widget.main_price,
      //                   textAlign: TextAlign.left,
      //                   overflow: TextOverflow.ellipsis,
      //                   maxLines: 1,
      //                   style: TextStyle(
      //                       color: MyTheme.accent_color,
      //                       fontSize: 14,
      //                       fontWeight: FontWeight.w600),
      //                 ),
      //               ),
      //              widget.has_discount ? Padding(
      //                 padding: EdgeInsets.fromLTRB(16, 0, 16, 8),
      //                 child: Text(
      //                   widget.stroked_price,
      //                   textAlign: TextAlign.left,
      //                   overflow: TextOverflow.ellipsis,
      //                   maxLines: 1,
      //                   style: TextStyle(
      //                     decoration:TextDecoration.lineThrough,
      //                       color: MyTheme.medium_grey,
      //                       fontSize: 11,
      //                       fontWeight: FontWeight.w600),
      //                 ),
      //               ):Container(),
      //             ],
      //           ),
      //         ),
      //       ]),
      // ),
      child: Container(
        width: 128.w,
        child: Column(
          children: [
            Center(
              child: ClipRRect(
                borderRadius:BorderRadius.circular(20.0),
                child: FadeInImage.assetNetwork(
                  placeholder: 'assets/placeholder.png',
                  height: 159.h,
                  width: 128.w,
                  image: widget.image,
                  fit: BoxFit.cover,
                ),
              ),
            ),
            addVerticalSpace(5.0.h),
          if(widget.rating != null)   Center(
                child: BAMRatingWidget(
                  rating: widget.rating.toString(),),
                ),
            if(widget.rating != null)   addVerticalSpace(5.0.h),

            Text(widget.name, overflow: TextOverflow.ellipsis, textAlign:widget.titleAlign, style: GoogleFonts.poppins(
              fontSize: 14.0,
              fontWeight: FontWeight.w700,

            ),),
            Row(
              mainAxisAlignment: widget.has_discount ? MainAxisAlignment.spaceBetween : MainAxisAlignment.center,
              children: [
              if(widget.has_discount)  Text(widget.has_discount ? widget.stroked_price : widget.main_price, style: GoogleFonts.poppins(
                    fontWeight: FontWeight.w500,
                    fontSize: 13.0,
                    color: Color(0xFFDB3022),
                    decoration: widget.has_discount ? TextDecoration.lineThrough : TextDecoration.none
                ),),
                Text(widget.main_price, style: GoogleFonts.poppins(
                  fontWeight: FontWeight.w500,
                  fontSize: 13.0,
                  color: Color(0xFFDB3022)
                ),)
              ],
            )
          ],
        ),
      ),
    );
  }
}
