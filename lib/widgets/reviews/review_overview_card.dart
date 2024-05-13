
import 'package:active_ecommerce_flutter/constants/colors.dart';
import 'package:active_ecommerce_flutter/screens/login.dart';
import 'package:active_ecommerce_flutter/utils/ui.dart';
import 'package:active_ecommerce_flutter/widgets/global/BAMElevatedButton.dart';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

class ReviewOverviewCard extends StatelessWidget {
  const ReviewOverviewCard({
    Key key,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: EdgeInsets.symmetric(vertical: 22.0, horizontal: 15.0),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20.0),
        boxShadow: <BoxShadow>[
          BoxShadow(
            color: Color(0XFF000000).withOpacity(0.12),
            offset: Offset(0.0, 1.0),
            blurRadius: 24.0,
          ),
        ],
      ),
      child: Column(
        children: [
          Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            mainAxisSize: MainAxisSize.min,
            children: [
              ClipRRect(
                borderRadius: BorderRadius.circular(10.0),
                child: Image.asset(
                  "assets/images/orders/african-long-dresses-women-traditional-african 2.png",
                  width: 68.0,
                  height: 121.0,
                  fit: BoxFit.fill,
                ),
              ),
              addHorizontalSpace(13.0),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  mainAxisAlignment: MainAxisAlignment.start,
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Text(
                      "Kente Dress (Full Gown)",
                      style: GoogleFonts.poppins(
                          fontSize: 15.0, fontWeight: FontWeight.w600),
                    ),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.start,
                      children: [
                        Icon(
                          Icons.star,
                          color: Colors.amber,
                          size: 14.0,
                        ),
                        addHorizontalSpace(2),
                        Icon(
                          Icons.star,
                          color: Colors.amber,
                          size: 14.0,
                        ),
                        addHorizontalSpace(2),
                        Icon(
                          Icons.star,
                          color: Colors.amber,
                          size: 14.0,
                        ),
                        addHorizontalSpace(2),
                        Icon(
                          Icons.star,
                          color: Colors.amber,
                          size: 14.0,
                        ),
                        addHorizontalSpace(2),
                        Icon(
                          Icons.star,
                          color: Colors.amber,
                          size: 14.0,
                        ),
                      ],
                    ),
                    addVerticalSpace(8.0),
                    Text(
                      '''The dress is great! Very classy and comfortable. It fit perfectly! I'm 5'7 and 130 pounds. I am a 34B chest. This dress would be too long for those who are shorter but could be hemmed...... ''',
                      style: GoogleFonts.poppins(
                          height: 1.5,
                          fontSize: 14.0,
                          fontWeight: FontWeight.w400,
                          color: Color(0xFF222222)
                      ),
                    )
                  ],
                ),
              )
            ],
          ),
          addVerticalSpace(10.0),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Row(
                children: [
                  ClipOval(child: Image.asset("assets/images/orders/[GetPaidStock1.png", width: 50.0, height: 50.0,)),
                  addHorizontalSpace(7.0),
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text("Gloria Boateng", style: GoogleFonts.poppins(
                          fontSize: 14.00,
                          fontWeight: FontWeight.w700
                      ),),
                      Text("December 5, 2021", style: GoogleFonts.poppins(
                          fontSize: 11.00,
                          fontWeight: FontWeight.w400,
                          color: defaultGrey
                      ),),
                    ],
                  )
                ],
              ),
              BAMElevatedButton(onPress: (){
              // Navigator.push(context, MaterialPageRoute(builder: (context)=>ReplyReviewScreen()));
              }, text: "Reply",
                size: Size(84.0, 36.0),)
            ],
          )
        ],
      ),
    );
  }
}
