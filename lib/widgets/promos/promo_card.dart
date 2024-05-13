
import 'package:active_ecommerce_flutter/constants/spacing.dart';
import 'package:active_ecommerce_flutter/screens/login.dart';
import 'package:active_ecommerce_flutter/utils/ui.dart';
import 'package:active_ecommerce_flutter/widgets/global/BAMElevatedButton.dart';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';


class PromoCard extends StatelessWidget {
  const PromoCard({
    Key key, @required this.imgUrl, @required this.timeLeft, @required this.title, @required this.type,
  }) : super(key: key);

  final String  imgUrl;
  final String timeLeft;
  final String title;
  final String type;


  @override
  Widget build(BuildContext context) {
    return Container(
      margin: EdgeInsets.only(bottom: 26.0),
      padding: EdgeInsets.only(right: 15.0),
      decoration: BoxDecoration(

        color: Colors.white,
        borderRadius: BorderRadius.circular(20.0),
        boxShadow: <BoxShadow>[
          BoxShadow(
            color: Color(0xFF000000).withOpacity(0.08),
            offset: Offset(0.0, 1.0),
            blurRadius: 25.0,
          ),
        ],
      ),
      child: Row(
        //TODO: make image take up fraction of width
        children: [
          Image.asset(
            "assets/images/promos/$imgUrl",
            width: (MediaQuery.of(context).size.width - defaultPadding *2) * 0.23,
            height: 88.0,
            fit: BoxFit.fill,
          ),
          addHorizontalSpace(12.0),
          Expanded(
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      title,
                      style: GoogleFonts.poppins(
                        fontSize: 14.0,
                        fontWeight: FontWeight.w500,
                      ),
                    ),
                    addVerticalSpace(8.0),
                    Text(
                      type,
                      style: GoogleFonts.poppins(
                        fontSize: 11.0,
                        fontWeight: FontWeight.w400,
                      ),
                    )
                  ],
                ),
                Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Text(
                      timeLeft,
                      style: GoogleFonts.poppins(
                          fontSize: 11.0,
                          fontWeight: FontWeight.w400,
                          color: Color(0xFF9B9B9B)),
                    ),
                    addVerticalSpace(7.0),
                    Flexible(
                        child: BAMElevatedButton(
                          onPress: () {},
                          text: "Edit",
                          size: Size(100.0, 42.0),
                        ))
                  ],
                )
              ],
            ),
          ),
        ],
      ),
    );
  }
}
