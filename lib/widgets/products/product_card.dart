
import 'package:active_ecommerce_flutter/constants/colors.dart';
import 'package:active_ecommerce_flutter/utils/ui.dart';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
class ProductCard extends StatelessWidget {
  //TODO: apply screen util
  const ProductCard(
      {Key key,
        @required this.imageUrl,
        @required this.amount,
        @required this.subTitle,
        @required this.title,
        this.tag})
      : super(key: key);
  final String imageUrl;
  final String amount;
  final String subTitle;
  final String title;
  final tag;

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: () {
        // Navigator.push(
        //     context,
        //     MaterialPageRoute(
        //         builder: (context) => ProductInfoScreen(
        //           imageUrl: imageUrl!,
        //           tag: tag,
        //           title: title!,
        //           subtitle: subTitle!,
        //           amount: amount!,
        //         )));
      },
      child: Column(
        mainAxisSize: MainAxisSize.min,
        crossAxisAlignment: CrossAxisAlignment.center,
        children: [
          //TODO: Mask images
          ClipRRect(
            borderRadius: BorderRadius.circular(20.0),
            child: Hero(
              tag: tag,
              child: Image.asset(
                imageUrl,
                width: MediaQuery.of(context).size.width * 0.4,
                height: 200.0,
                fit: BoxFit.fill,
              ),
            ),
          ),
          addVerticalSpace(12),
          Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Text(
                subTitle,
                style: GoogleFonts.poppins(
                  fontSize: 11.0,
                  fontWeight: FontWeight.w400,
                  color: defaultGrey,
                  height: 1.5,
                ),
              ),
              addHorizontalSpace(6),
              Text(
                "GHC$amount",
                style: GoogleFonts.poppins(
                  fontSize: 11.0,
                  fontWeight: FontWeight.w400,
                  color: defaultGrey,
                  height: 1.5,
                ),
              )
            ],
          ),
          Text(
            title,
            style: GoogleFonts.poppins(
                fontSize: 14.0, fontWeight: FontWeight.w700, height: 1.5),
          )
        ],
      ),
    );
  }
}
